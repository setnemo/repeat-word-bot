<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Validators;

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
        return "`Сброс прогресса:`\nИспользуйте команду `/reset collection <number>` или " .
            "`/reset collection <number>`. Будьте осторожны, сброс не обратим";
    }
}
