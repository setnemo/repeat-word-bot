<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Messages;

/**
 * Class DelMessage
 * @package RepeatBot\Bot\Service\CommandService\Messages
 */
class DelMessage
{
    public const ERROR_TEXT = "`Скидання прогресу:`\nВикористовуйте команду `/del collection <number>` " .
    "або `/del my progress`. Будьте обережні, скидання неможливо скасувати";
}
