<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Messages;

/**
 * Class ResetMessage
 * @package RepeatBot\Bot\Service\CommandService\Messages
 */
class ResetMessage
{
    public const ERROR_TEXT = "`Сброс прогресса:`\nИспользуйте команду `/reset collection <number>` или " .
    "`/reset collection <number>`. Будьте осторожны, сброс не обратим";
}
