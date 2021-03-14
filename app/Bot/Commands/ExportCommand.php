<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class ExportCommand
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\ExportService
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class ExportCommand extends SystemCommand
{
    protected $usage = '/export';

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
                command: 'export',
                payload: explode(' ', BotHelper::getTextFromInput($this->getMessage()->getText(true))),
                chatId: $this->getMessage()->getChat()->getId(),
            )
        );

        return $command->executeCommand($command->makeService());
    }
}
