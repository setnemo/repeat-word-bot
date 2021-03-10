<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Validators;

use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;

class AlarmValidator implements ValidateCommand
{
    public function validate(CommandOptions $options): array
    {
        $payload = $options->getPayload();
        if ($payload[0] !== 'list' && $payload[0] !== 'reset') {
            $textToArray = $options->getPayload();
            $commands = array_reverse($textToArray);
            if ($this->isBrokenTime($commands)) {
                return [
                    new ResponseDirector('sendMessage', [
                        'chat_id' => $options->getChatId(),
                        'text' => $this->getErrorText(),
                        'parse_mode' => 'markdown',
                        'disable_web_page_preview' => true,
                        'disable_notification' => 1,
                    ])
                ];
            }
        }

        return [];
    }

    private function isBrokenTime(array $commands): bool
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

    private function getErrorText(): string
    {
        return "Чтобы создать напоминание на 9 утра - воспользуйтесь командой `/alarm 9:00`. " .
            "Чтобы создать напоминание на 9 вечера - воспользуйтесь командой `/alarm 21:00`.\n\n" .
            "По умолчанию используется часовой пояс FLE Standard Time (Kyiv), то есть по сути команды выше на самом деле " .
            "можно отправить с кодом FDT `/alarm FDT 9:00` и результат будет тот же. Если же вам нужно получать " .
            "оповещения по другому часовому поясу, например MSK, то нужно писать так `/alarm MSK 9:00`\n\n" .
            "Посмотреть все коды часовых поясов можно командой /time\n\n" .
            "Посмотреть свои напоминания /alarm list\n\n" .
            "Удалить свои напоминания /alarm reset";
    }
}