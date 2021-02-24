<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;

/**
 * Class HelpCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class HelpCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'help';
    /**
     * @var string
     */
    protected $description = 'Help command';
    /**
     * @var string
     */
    protected $usage = '/help';
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
        $chat_id = $this->getMessage()->getChat()->getId();
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $text = "`Справка по использованию бота:`\n\n";
        $text .= "вызвать справку во время тренировки:\n - /help\n\n";
        $text .= "пропустить слово:\n - Нажать кнопку \[Я не знаю]\n - Написать русскую букву Х\n";
        $text .= " - Написать английскую букву P\n - Написать точку\n - Написать 'не знаю' или 'dont know'\n\n";
        $text .= "отправить слово в итерацию never (1 год до повторения):\n - Написать 1\n\n";
        $text .= "сбросить прогресс:\n - /reset my progress\n\n";
        $text .= "посмотреть прогресс:\n - /progress\n - зайти в \[Мой прогресс] в меню тренировок(/training)\n\n";
        $text .= "включить уведомления со звуком:\n - /settings\n - зайти в \[Настройки] в главном меню (/start)\n\n";

        $text .= "\n\n";
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ];
        return Request::sendMessage($data);
    }
}
