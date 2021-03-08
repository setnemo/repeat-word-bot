<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\ORM\Entities\UserNotification;

/**
 * Class SettingsCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class SettingsCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'Settings';
    /**
     * @var string
     */
    protected $description = 'Settings command';
    /**
     * @var string
     */
    protected $usage = '/settings';
    /**
     * @var string
     */
    protected $version = '1.0.0';
    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $userId = $this->getMessage()->getFrom()->getId();
        $userNotificationRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(UserNotification::class);
        $silent = $userNotificationRepository->getOrCreateUserNotification(
            $userId
        )->getSilent();
        $config = App::getInstance()->getConfig();
        $cache = Cache::getInstance()->init($config);
        $priority = $cache->getPriority($userId);
        $symbolSilent = $silent === 1 ? '✅' : '❌';
        $symbolPriority = $priority === 1 ? '✅' : '❌';
        $chat_id = $this->getMessage()->getChat()->getId();
        $textSilent = "Тихий режим сообщений: {$symbolSilent}";
        $texPriority = "Приоритет меньших итераций: {$symbolPriority}";
        $texVoices = "Выбрать голоса для тренировок";
        /** @psalm-suppress TooManyArguments */
        $keyboard = new InlineKeyboard(...BotHelper::getSettingsKeyboard(
            $textSilent,
            $texPriority,
            $texVoices,
            $silent === 1 ? 0 : 1,
            $priority === 1 ? 0 : 1,
        ));
        $data = [
            'chat_id' => $chat_id,
            'text' => BotHelper::getSettingsText(),
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ];
        return Request::sendMessage($data);
    }
}
