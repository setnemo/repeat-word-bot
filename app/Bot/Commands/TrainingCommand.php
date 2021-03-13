<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class TrainingCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class TrainingCommand extends SystemCommand
{
    protected $usage = '/training';

    /**
     * Command execute method
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $command = new CommandService(
            options: new CommandOptions(
                command: 'training',
                chatId: $this->getMessage()->getChat()->getId()
            )
        );
    
        return $command->executeCommand($command->makeService());
    }
}
