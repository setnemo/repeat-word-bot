<?php

declare(strict_types=1);

namespace RepeatBot\Core;

use Longman\TelegramBot\Entities\Chat;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Psr\Log\LoggerInterface;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\ExportService;
use RepeatBot\Common\Config;
use RepeatBot\Common\Singleton;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Database\Model\LearnNotification;
use RepeatBot\Core\Database\Model\LearnNotificationPersonal;
use RepeatBot\Core\Database\Repository\ExportRepository;
use RepeatBot\Core\Database\Repository\LearnNotificationPersonalRepository;
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
     * @param Config          $config
     * @param LoggerInterface $logger
     *
     * @return Bot
     */
    public function init(Config $config, LoggerInterface $logger): self
    {
        $this->logger = $logger;
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
            $this->telegram->enableLimiter();
//            $this->getSetUpdateFilter();
        } catch (TelegramException $e) {
            $logger->error($e->getMessage(), $e->getTrace());
        }

        return $this;
    }


    public function botNotify()
    {
        $this->checkVersion();
        $this->handleNotifications();
        $this->handleNotificationsPersonal();
    
    }

    /**
     * @param ExportRepository   $exportRepository
     * @param ExportService      $service
     *
     * @throws TelegramException
     * @throws \Mpdf\MpdfException
     */
    public function queue(ExportRepository $exportRepository, ExportService $service): void
    {
        $exports = $exportRepository->getExports();
        foreach ($exports as $export) {
            $service->execute($export);
        }
    }

    /**
     *
     */
    public function runHook(): void
    {
        $this->register('repeat-webhook');
        try {
            $this->telegram->handle();
            Metric::getInstance()->increaseMetric('webhook');
        } catch (TelegramException $e) {
            Log::getInstance()->getLogger()->error($e->getMessage(), $e->getTrace());
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
        $database = $this->db;
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
                $userNotificationRepository->deleteUserNotification($notification->getUserId());
            }
        }
    }

    private function handleNotificationsPersonal(): void
    {
        $database = $this->db;
        $learnNotificationRepository = new LearnNotificationPersonalRepository($database);
        $notifications = $learnNotificationRepository->getNotifications();
        $userNotificationRepository = new UserNotificationRepository($database);
        $tzs = BotHelper::getTimeZones();
        /** @var LearnNotificationPersonal $notification */
        foreach ($notifications as $notification) {
            $currentTz = $notification->getTimezone();
            $tmp = array_filter($tzs, function (array $item) use ($currentTz) {
                return $currentTz === $item['abbr'];
            });
            $new = [];
            foreach ($tmp as $item) {
                $new = $item;
            }
            date_default_timezone_set($new['utc'][0]);
            if (strtotime($notification->getAlarm()) < time()) {
                $silent = $userNotificationRepository->getOrCreateUserNotification(
                    $notification->getUserId()
                )->getSilent();
                Request::sendMessage([
                    'chat_id' => $notification->getUserId(),
                    'text' => $notification->getMessage(),
                    'disable_notification' => $silent
                ]);
                $learnNotificationRepository->updateNotification($notification);
            }
        }
    }
    
    private function getSetUpdateFilter(): void
    {
        $this->telegram->setUpdateFilter(static function (Update $array) {
            $bannedIds = ['1239727062'];
            $flag = true;
            if ($array->getMessage()) {
                if (in_array($array->getMessage()->getFrom()->getId(), $bannedIds)) {
                    $flag = false;
                }
            }
            if ($flag === false) {
                $data = [
                    'chat_id' => 1239727062,
                    'text' => 'Permanently banned',
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'disable_notification' => 1,
                ];
                Request::sendMessage($data);
            }
            return $flag;
        });
    }
    
    
    
    /**
     * @param string $prefix
     */
    private function register(string $prefix): void
    {
        /** @var Client $cache */
        $cache = Cache::getInstance()->getConnection();
        $key = $prefix . '_registered';
        if (!$cache->exists($key)) {
            try {
                $hook_url = "https://repeat.webhook.pp.ua";
                $result = $this->telegram->setWebhook($hook_url);
                if ($result->isOk()) {
                    $cache->set($key, $result->getDescription());
                }
            } catch (TelegramException $e) {
                Log::getInstance()->getLogger()->error('Registered failed', ['error' => $e->getMessage()]);
            }
        }
    }
}
