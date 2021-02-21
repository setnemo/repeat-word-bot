<?php

declare(strict_types=1);

namespace RepeatBot\Core;

use FaaPz\PDO\Database as DB;
use Longman\TelegramBot\Entities\Chat;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Psr\Log\LoggerInterface;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Common\Config;
use RepeatBot\Common\Singleton;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Database\Model\LearnNotification;
use RepeatBot\Core\Database\Repository\LearnNotificationRepository;
use RepeatBot\Core\Database\Repository\TrainingRepository;
use RepeatBot\Core\Database\Repository\UserNotificationRepository;
use RepeatBot\Core\Database\Repository\VersionNotificationRepository;
use RepeatBot\Core\Database\Repository\VersionRepository;

/**
 * Class Bot
 * @package RepeatBot\Core
 */
final class Bot extends Singleton
{
    /**
     * @var Telegram
     */
    private Telegram $telegram;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var DB
     */
    private DB $db;

    /**
     * @param Config          $config
     * @param LoggerInterface $logger
     *
     * @return Bot
     */
    public function init(Config $config, LoggerInterface $logger): self
    {
        $this->logger = $logger;
        $this->db = Database::getInstance()->init($config)->getConnection();
        try {
            $bot_api_key = $config->getKey('telegram.token');
            $bot_username = $config->getKey('telegram.bot_name');
            $this->telegram = new Telegram($bot_api_key, $bot_username);
            TelegramLog::initialize($logger);
            $this->telegram->enableAdmin(intval($config->getKey('telegram.admin_id')));
            $this->telegram->addCommandsPaths([$config->getKey('telegram.command_path'),]);
            $this->telegram->enableMySql(
                [
                    'host'     => $config->getKey('database.host'),
                    'user'     => $config->getKey('database.user'),
                    'password' => $config->getKey('database.password'),
                    'database' => $config->getKey('database.name'),
                ]
            );
            $this->checkVersion();
            $this->handleNotifications();
        } catch (TelegramException $e) {
            $logger->error($e->getMessage(), $e->getTrace());
        }

        return $this;
    }

    /**
     *
     */
    public function run(): void
    {
        try {
            $this->telegram->handleGetUpdates();
        } catch (TelegramException $e) {
            $this->getLogger()->error($e->getMessage(), $e->getTrace());
        }
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @throws TelegramException
     */
    private function checkVersion(): void
    {
        $repositoryVersion = new VersionRepository($this->db);
        $version = $repositoryVersion->getNewLatestVersion();
        if (!$version->isEmpty()) {
            /** @psalm-suppress TooManyArguments */
            $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
            $keyboard->setResizeKeyboard(true);

            $results = Request::sendToActiveChats(
                'sendMessage',
                [
                    'text' => $version->getDescription(),
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'reply_markup' => $keyboard,
                ],
                [
                    'groups' => true,
                    'supergroups' => true,
                    'channels' => false,
                    'users' => true,
                ]
            );
            $repositoryNotification = new VersionNotificationRepository($this->db);
            foreach ($results as $result) {
                if ($result->isOk()) {
                    /** @var Message $message */
                    $message = $result->getResult();
                    $chat = $message->getChat() ;
                    if (!empty($chat)) {
                        /** @var Chat $chat */
                        $chatId = $chat->getId();
                        try {
                            $model = $repositoryNotification->getNewModel([
                                'chat_id' => $chatId,
                                'version_id' => $version->getId(),
                            ]);
                            $repositoryNotification->saveVersionNotification($model);
                        } catch (\Throwable $e) {
                            Log::getInstance()->getLogger()->error($e->getMessage(), $e->getTrace());
                        }
                    }
                }
            }
            $repositoryVersion->applyVersion($version);
        }
    }

    /**
     * @throws TelegramException
     */
    private function handleNotifications(): void
    {
        $database = Database::getInstance()->getConnection();
        $learnNotificationRepository = new LearnNotificationRepository($database);
        $trainingRepository = new TrainingRepository($database);
        $userNotificationRepository = new UserNotificationRepository($database);
        $userNotifications = $userNotificationRepository->getUserNotifications();
        $inactiveUser = $trainingRepository->getInactiveUsers($userNotifications);
        $newNotifications = $learnNotificationRepository->filterNotifications($inactiveUser);
        $learnNotificationRepository->saveNotifications($newNotifications);
        $notifications = $learnNotificationRepository->getUnsentNotifications();
        /** @var LearnNotification $notification */
        foreach ($notifications as $notification) {
            $result = Request::sendMessage([
                'chat_id' => $notification->getUserId(),
                'text' => $notification->getMessage(),
                'disable_notification' => $notification->getSilent()
            ]);
            $learnNotificationRepository->updateNotification($notification);
            if (!$result->isOk()) {
                $userNotificationRepository->deleteUserNotification($notification);
            }
        }
    }
}
