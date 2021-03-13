<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Exception;
use Longman\TelegramBot\Entities\Keyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;

/**
 * Class TimeService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class TimeServiceDefault extends BaseDefaultCommandService
{
    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function execute(): CommandInterface
    {
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);

        $data = [
            'chat_id' => $this->getOptions()->getChatId(),
            'text' => $this->getText(),
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ];

        $this->setResponse(new ResponseDirector('sendMessage', $data));

        return $this;
    }

    /**
     * @return string
     */
    private function getText(): string
    {
        $text = "Список поддерживаемых аббривиатур для выбора часового пояса в персональных напоминаниях:\n\n";
        $timezones = BotHelper::getTimeZones();
        foreach ($timezones as $timezone) {
            $text .= strtr("`:abbr:` :text\n", [
                ':abbr' => $timezone['abbr'],
                ':text' => $timezone['text'],
            ]);
        }
        $text .= "\nДля напоминаний используйте буквенный код, например MSK (Moscow), тогда команда будет /alarm MSK 9:00";

        return $text;
    }
}
