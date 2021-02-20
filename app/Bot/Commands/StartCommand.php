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
 * Class StartCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class StartCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'start';
    /**
     * @var string
     */
    protected $description = 'Start command';
    /**
     * @var string
     */
    protected $usage = '/start';
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
        $text = "Привет! Я помогаю учить английские слова по методике интервального повторения.\n\n";
        $text .= "Интервальные повторения (англ. spaced repetition) — техника удержания в памяти, ";
        $text .= "заключающаяся в повторении запомненного учебного материала по определённым, ";
        $text .= "постоянно возрастающим интервалам.\n\nКак только вы правильно отвечаете, ";
        $text .= 'слово переходит на следующую итерацию для повторения. Всего итераций 7. ';
        $text .= 'Первая через сутки, вторая через три дня, потом через семь, потом через месяц, три, полгода и год.';
        $text .= "\n\nТакже пользуйтесь кнопкой 'Я не знаю'. Она поможет Вам разобраться с новыми для Вас словами. ";
        $text .= ' А также не добавляйте сразу слишком много, сначала отправьте на долгие итерации небольшие коллекции. Удачи!';
        $text .= "\n\nP.S. Если вдруг слово не воспроизводится - отправляйте это сообщение форвардом @omentes в личку.";
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
