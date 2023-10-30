<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Validators;

use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\Messages\AlarmMessage;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;
use TelegramBot\CommandWrapper\Validator\ValidateCommand;

class AlarmValidator implements ValidateCommand
{
    /**
     * {@inheritDoc}
     * @throws SupportTypeException
     */
    public function validate(CommandOptions $options): array
    {
        $payload = $options->getPayload();
        if ($payload[0] !== 'list' && $payload[0] !== 'reset') {
            $textToArray = $options->getPayload();
            $commands    = array_reverse($textToArray);
            if ($this->isBrokenTime($commands)) {
                return [
                    new ResponseDirector('sendMessage', [
                        'chat_id'                  => $options->getChatId(),
                        'text'                     => $this->getErrorText(),
                        'parse_mode'               => 'markdown',
                        'disable_web_page_preview' => true,
                        'disable_notification'     => 1,
                    ]),
                ];
            }
        }

        return [];
    }

    /**
     * @param array $commands
     *
     * @return bool
     */
    protected function isBrokenTime(array $commands): bool
    {
        if (empty($commands[0])) {
            return true;
        }
        if (!preg_match("/^(?:2[0-3]|[01][0-9]|[0-9]):[0-5][0-9]$/", $commands[0])) {
            return true;
        }

        $times = explode(':', $commands[0]);
        if (intval($times[0]) < 0 || intval($times[0]) > 23 || intval($times[1]) < 0 || intval($times[1]) > 59) {
            return true;
        }
        if (isset($commands[1])) {
            $abbrs = array_column(BotHelper::getTimeZones(), 'abbr');
            if (!in_array($commands[1], $abbrs)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getErrorText(): string
    {
        return AlarmMessage::ERROR_TEXT;
    }
}
