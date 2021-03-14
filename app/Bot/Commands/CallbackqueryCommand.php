<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class CallbackqueryCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $command = new CommandService(options: new CommandOptions(
            payload: explode('_', $this->getCallbackQuery()->getData()),
            chatId: $this->getCallbackQuery()->getMessage()->getChat()->getId(),
            messageId: $this->getCallbackQuery()->getMessage()->getMessageId(),
            callbackQueryId: intval($this->getCallbackQuery()->getId())
        ), type: 'query');

        return $command->executeCommand($command->makeService());
    }
}
