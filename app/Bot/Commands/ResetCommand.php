<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class ResetCommand
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\ResetService
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class ResetCommand extends SystemCommand
{
    protected $usage = '/reset';

    /**
     * Command execute method
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $command = new CommandService(
            options: new CommandOptions(
                command: 'reset',
                payload: explode(' ', BotHelper::getTextFromInput($this->getMessage()->getText(true))),
                chatId: $this->getMessage()->getChat()->getId(),
            )
        );

        return $command->executeCommand($command->makeService());
    }
}
