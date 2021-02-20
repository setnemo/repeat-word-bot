<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Database\Repository\UserNotificationRepository;

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
        $database = Database::getInstance()->getConnection();
        $userNotificationRepository = new UserNotificationRepository($database);
        $switcher = $userNotificationRepository->getOrCreateUserNotification(
            $this->getMessage()->getFrom()->getId()
        )->getSilent();
        $symbol = $switcher === 1 ? '✅' : '❌';
        $chat_id = $this->getMessage()->getChat()->getId();
        $text = "Тихий режим сообщений: {$symbol}";
        /** @psalm-suppress TooManyArguments */
        $keyboard = new InlineKeyboard(BotHelper::getSettingsKeyboard($text, $switcher === 1 ? 0 : 1));
        $data = [
            'chat_id' => $chat_id,
            'text' => 'В настройках можно отключить тихий режим получения уведомлений. По умолчанию тихий режим включен для всех. Для переключения режима нажмите на кнопку',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ];
        return Request::sendMessage($data);
    }
}
