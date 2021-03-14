<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class HelpCommand
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\HelpService
 * @package Longman\TelegramBot\Commands\SystemCommands
 */
class HelpCommand extends SystemCommand
{
    protected $usage = '/help';

    /**
     * Command execute method
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $command = new CommandService(
            options: new CommandOptions(
                command: 'help',
                chatId: $this->getMessage()->getChat()->getId(),
            )
        );

        return $command->executeCommand($command->makeService());
    }
}
