<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class SettingsCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class SettingsCommand extends SystemCommand
{
    protected $usage = '/settings';

    /**
     * Command execute method
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $command = new CommandService(
            options: new CommandOptions(
                command: 'settings',
                chatId: $this->getMessage()->getChat()->getId()
            )
        );

        return $command->executeCommand($command->makeService());
    }
}
