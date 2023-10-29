<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Messages;

/**
 * Class ResetMessage
 * @package RepeatBot\Bot\Service\CommandService\Messages
 */
class ResetMessage
{
    public const ERROR_TEXT = "`Скидання прогресу:`\nВикористовуйте команду `/reset collection <number>` або " .
    "`/reset collection <number>`. Будьте обережні, скидання неможливо скасувати";
}
