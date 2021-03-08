<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Validators;

use RepeatBot\Bot\Service\CommandService\CommandOptions;

interface ValidateCommand
{
    public function validate(CommandOptions $options);
}
