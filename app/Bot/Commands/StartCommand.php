<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use RepeatBot\Bot\Service\CommandService;
use TelegramBot\CommandWrapper\Command\CommandOptions;

/**
 * Class StartCommand
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\StartService
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class StartCommand extends SystemCommand
{
    protected $usage = '/start';

    /**
     * Command execute method
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $command = new CommandService(
            options: new CommandOptions(
                command: 'start',
                chatId: $this->getMessage()->getChat()->getId()
            )
        );

        return $command->executeCommand($command->makeService());
    }
}
