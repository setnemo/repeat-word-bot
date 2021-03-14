<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Messages;

/**
 * Class DelMessage
 * @package RepeatBot\Bot\Service\CommandService\Messages
 */
class DelMessage
{
    public const ERROR_TEXT = "`Сброс прогресса:`\nИспользуйте команду `/del collection <number>` " .
    "или `/del my progress`. Будьте осторожны: сброс не обратим";
}
