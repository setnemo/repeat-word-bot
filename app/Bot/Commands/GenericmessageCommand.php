<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

class GenericmessageCommand extends SystemCommand
{
    /**
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $command = new CommandService(
            options: new CommandOptions(
                text: $this->getMessage()->getText(false) ?? '',
                chatId: $this->getMessage()->getFrom()->getId()
            ),
            type: 'generic'
        );
    
        return $command->executeCommand($command->makeService());
    }
}
