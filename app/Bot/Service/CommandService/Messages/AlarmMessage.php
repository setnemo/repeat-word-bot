<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Messages;

/**
 * Class AlarmMessage
 * @package RepeatBot\Bot\Service\CommandService\Messages
 */
class AlarmMessage
{
    public const ERROR_TEXT = "Чтобы создать напоминание на 9 утра - воспользуйтесь командой `/alarm 9:00`. " .
        "Чтобы создать напоминание на 9 вечера - воспользуйтесь командой `/alarm 21:00`.\n\n" .
        "По умолчанию используется часовой пояс FLE Standard Time (Kyiv), то есть по сути команды выше на самом деле " .
        "можно отправить с кодом FDT `/alarm FDT 9:00` и результат будет тот же. Если же вам нужно получать " .
        "оповещения по другому часовому поясу, например MSK, то нужно писать так `/alarm MSK 9:00`\n\n" .
        "Посмотреть все коды часовых поясов можно командой /time\n\n" .
        "Посмотреть свои напоминания /alarm list\n\n" .
        "Удалить свои напоминания /alarm reset";
}
