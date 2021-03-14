<?php

declare(strict_types=1);

namespace RepeatBot\Core;

use Carbon\Carbon;
use Longman\TelegramBot\Entities\Chat;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Predis\Client;
use Psr\Log\LoggerInterface;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\ExportQueueService;
use RepeatBot\Common\Config;
use RepeatBot\Common\Singleton;
use RepeatBot\Core\ORM\Entities\LearnNotification;
use RepeatBot\Core\ORM\Entities\VersionNotification;
use RepeatBot\Core\ORM\Repositories\ExportRepository;

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
            /**
             *   $this->telegram->enableLimiter();
             *   $this->getSetUpdateFilter();
             */
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
     * @param ExportQueueService $service
     *
     * @throws TelegramException
     * @throws \Mpdf\MpdfException
     */
    public function queue(ExportRepository $exportRepository, ExportQueueService $service): void
    {
        $exports = $exportRepository->getExports();
        foreach ($exports as $export) {
            try {
                $service->execute($export);
            } catch (\Throwable $t) {
                echo $t->getMessage();
            }
        }
    }

    /**
     * @param Config $config
     */
    public function runHook(Config $config): void
    {
        $this->register($config, 'repeat-webhook');
        try {
            $this->telegram->handle();
            Metric::getInstance()->increaseMetric('webhook');
        } catch (\Throwable $e) {
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
        $repositoryVersion = Database::getInstance()
            ->getEntityManager()
            ->getRepository(ORM\Entities\Version::class);

        $version = $repositoryVersion->getNewLatestVersion();
        if ($version) {
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
            $repositoryNotification = Database::getInstance()
                ->getEntityManager()
                ->getRepository(ORM\Entities\VersionNotification::class);
            foreach ($results as $result) {
                if ($result->isOk()) {
                    /** @var Message $message */
                    $message = $result->getResult();
                    $chat = $message->getChat() ;
                    if (!empty($chat)) {
                        /** @var Chat $chat */
                        $chatId = $chat->getId();
                        try {
                            $entity = new VersionNotification();
                            $entity->setChatId($chatId);
                            $entity->setVersionId($version->getId());
                            $entity->setCreatedAt(Carbon::now(Database::DEFAULT_TZ));
                            $repositoryNotification->saveVersionNotification($entity);
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
        $learnNotificationRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(ORM\Entities\LearnNotification::class);

        $trainingRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(ORM\Entities\Training::class);

        $userNotificationRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(ORM\Entities\UserNotification::class);


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
        $learnNotificationRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(ORM\Entities\LearnNotificationPersonal::class);

        $notifications = $learnNotificationRepository->getNotifications();
        $userNotificationRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(ORM\Entities\UserNotification::class);
        $tzs = BotHelper::getTimeZones();

        /** @var ORM\Entities\LearnNotificationPersonal $notification */
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
            if ($notification->getAlarm()->getTimestamp() < time()) {
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
     * @param Config $config
     * @param string $prefix
     *
     * @throws TelegramException
     */
    private function register(Config $config, string $prefix): void
    {
        /** @var Client $cache */
        $cache = Cache::getInstance()->getRedis();
        $key = $prefix . '_registered';
        if (!$cache->exists($key)) {
            $this->telegram->deleteWebhook();
            try {
                $hook_url = $config->getKey('HOOK_HOST');
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
