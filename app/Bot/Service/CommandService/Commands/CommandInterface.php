<?php

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\Service\CommandService\Validators\ValidateCommand;

interface CommandInterface
{
    public function validate(ValidateCommand $validator): self;

    public function execute(): self;

    public function postStackMessages(): self;

    public function getResponseMessage(): ServerResponse;

    public function hasResponse(): bool;
}
