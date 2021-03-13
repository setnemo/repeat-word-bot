<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\GenericMessageDirectorFabric;

class GenericmessageCommand extends SystemCommand
{
    /**
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        return (new CommandService())->execute(
            options: new CommandOptions(
                text: $this->getMessage()->getText(false) ?? '',
                chatId: $this->getMessage()->getFrom()->getId()
            ),
            type: 'generic'
        );
    }
}
