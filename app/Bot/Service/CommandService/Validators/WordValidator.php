<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Validators;

use RepeatBot\Bot\Service\CommandService\Commands\WordService;
use RepeatBot\Common\Config;
use RepeatBot\Core\App;
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
    protected Config $config;

    public function __construct()
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->config = App::getInstance()->getConfig();
    }

    /**
     * {@inheritDoc}
     * @throws SupportTypeException
     */
    public function validate(CommandOptions $options): array
    {
        if (!in_array($options->getChatId(), explode(',', $this->config->getKey('allowed_update_words')))) {
            return $this->createUserErrorResponse($options);
        }

        if (
            empty($options->getPayload()[WordService::CMD]) ||
            !in_array(strtolower($options->getPayload()[WordService::CMD] ?? ''), [WordService::UPDATE, WordService::SHOW])
        ) {
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
    protected function createUserErrorResponse(CommandOptions $options, string $text = 'До побачення'): array
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
