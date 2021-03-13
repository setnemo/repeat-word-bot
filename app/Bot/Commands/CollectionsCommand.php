<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class CollectionsCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class CollectionsCommand extends SystemCommand
{
    protected $usage = '/collections';

    /**
     * Command execute method
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $command = new CommandService(
            options: new CommandOptions(
                command: 'collections',
                chatId: $this->getMessage()->getChat()->getId(),
            )
        );
    
        return $command->executeCommand($command->makeService());
    }
}
