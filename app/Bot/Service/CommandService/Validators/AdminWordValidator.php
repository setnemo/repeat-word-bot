<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Validators;

use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;
use TelegramBot\CommandWrapper\Validator\ValidateCommand;

/**
 * Class ExportValidator
 * @package RepeatBot\Bot\Service\CommandService\Validators
 */
class AdminWordValidator implements ValidateCommand
{
    /**
     * {@inheritDoc}
     * @throws SupportTypeException
     */
    public function validate(CommandOptions $options): array
    {
        if (in_array($options->getChatId(), [281861745])) {
            return $this->createUserErrorResponse($options);
        }

        return [];
    }

    /**
     * @param CommandOptions $options
     *
     * @return ResponseDirector[]
     * @throws SupportTypeException
     */
    private function createUserErrorResponse(CommandOptions $options): array
    {
        $data = [
            'chat_id'              => $options->getChatId(),
            'text'                 => 'До побачення',
            'parse_mode'           => 'markdown',
            'disable_notification' => 1,
        ];

        return [
            new ResponseDirector('sendMessage', $data),
        ];
    }
}
