<?php

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\Service\CommandService\Validators\ValidateCommand;

interface CommandInterface
{
    /**
     * @param ValidateCommand|null $validator
     *
     * @return $this
     */
    public function validate(?ValidateCommand $validator): CommandInterface;

    /**
     * @return $this
     */
    public function execute(): CommandInterface;

    /**
     * @return $this
     */
    public function postStackMessages(): CommandInterface;

    /**
     * @return ServerResponse
     */
    public function getResponseMessage(): ServerResponse;

    /**
     * @return bool
     */
    public function hasResponse(): bool;

    /**
     * @return array
     */
    public function showResponses(): array;
}
