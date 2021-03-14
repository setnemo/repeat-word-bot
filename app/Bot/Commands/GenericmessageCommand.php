<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class GenericmessageCommand
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\StartService
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\HelpService
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\ProgressService
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\CollectionService
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\SettingsService
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\TrainingService
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\TranslateTrainingService
 * @uses \RepeatBot\Bot\Service\CommandService\GenericMessageDirectorFabric
 * @package Longman\TelegramBot\Commands\SystemCommands
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $command = new CommandService(
            options: new CommandOptions(
                text: $this->getMessage()->getText(false) ?? '',
                chatId: $this->getMessage()->getFrom()->getId()
            ),
            type: 'generic'
        );

        return $command->executeCommand($command->makeService());
    }
}
