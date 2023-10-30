<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Validators;

use RepeatBot\Bot\Service\CommandService\Commands\WordService;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;
use TelegramBot\CommandWrapper\Validator\ValidateCommand;

/**
 * Class ExportValidator
 * @package RepeatBot\Bot\Service\CommandService\Validators
 */
class WordValidator implements ValidateCommand
{
    /**
     * {@inheritDoc}
     * @throws SupportTypeException
     */
    public function validate(CommandOptions $options): array
    {
        if (!in_array($options->getChatId(), [281861745, 503910905, 450937864])) {
            return $this->createUserErrorResponse($options);
        }

        if (empty($options->getPayload()[WordService::CMD]) || !in_array(strtolower($options->getPayload()[WordService::CMD] ?? ''), [WordService::UPDATE, WordService::SHOW])) {
            return $this->createUserErrorResponse($options, 'Помилка команди');
        }

        return [];
    }

    /**
     * @param CommandOptions $options
     * @param string $text
     * @return ResponseDirector[]
     * @throws SupportTypeException
     */
    private function createUserErrorResponse(CommandOptions $options, string $text = 'До побачення'): array
    {
        $data = [
            'chat_id'              => $options->getChatId(),
            'text'                 => $text,
            'parse_mode'           => 'markdown',
            'disable_notification' => 1,
        ];

        return [
            new ResponseDirector('sendMessage', $data),
        ];
    }
}
