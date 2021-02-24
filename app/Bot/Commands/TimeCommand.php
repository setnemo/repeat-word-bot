<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\App;
use RepeatBot\Core\Metric;

/**
 * Class TimeCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class TimeCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'time';
    /**
     * @var string
     */
    protected $description = 'Time command';
    /**
     * @var string
     */
    protected $usage = '/time';
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
        $config = App::getInstance()->getConfig();
        $metric = Metric::getInstance()->init($config);
        $metric->increaseMetric('usage');

        $chat_id = $this->getMessage()->getChat()->getId();
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $text = "Список поддерживаемых аббривиатур для выбора часового пояса в персональных напоминаниях:\n\n";
        $timezones = BotHelper::getTimeZones();
        foreach ($timezones as $timezone) {
            $text .= strtr("`:abbr:` :text\n", [
                ':abbr' => $timezone['abbr'],
                ':text' => $timezone['text'],
            ]);
        }
        $text .= "\nДля напоминаний используйте буквенный код, например MSK (Moscow), тогда команда будет /alarm MSK 9:00";
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
