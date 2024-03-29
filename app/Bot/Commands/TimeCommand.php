<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\Service\CommandService;
use TelegramBot\CommandWrapper\Command\CommandOptions;

/**
 * Class TimeCommand
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\TimeService
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class TimeCommand extends SystemCommand
{
    protected $usage = '/time';

    /**
     * Command execute method
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $command = new CommandService(
            options: new CommandOptions(
                command: 'time',
                chatId: $this->getMessage()->getChat()->getId(),
            )
        );

        return $command->executeCommand($command->makeService());
    }
}
