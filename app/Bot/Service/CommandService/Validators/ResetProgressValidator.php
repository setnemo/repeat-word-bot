<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Validators;

use RepeatBot\Bot\Service\CommandService\Messages\ResetMessage;

/**
 * Class ResetProgressValidator
 * @package RepeatBot\Bot\Service\CommandService\Validators
 */
class ResetProgressValidator extends DelProgressValidator
{
    /**
     * @return string
     */
    protected function getErrorText(): string
    {
        return ResetMessage::ERROR_TEXT;
    }
}
