<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class AlarmCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class AlarmCommand extends SystemCommand
{
    protected $usage = '/alarm';

    /**
     * Command execute method
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $command = new CommandService(
            options: new CommandOptions(
                command: 'alarm',
                payload: explode(' ', BotHelper::getTextFromInput($this->getMessage()->getText(true))),
                chatId: $this->getMessage()->getChat()->getId(),
            )
        );

        return $command->executeCommand($command->makeService());
    }
}
