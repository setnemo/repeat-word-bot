<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\Service\CommandService;
use TelegramBot\CommandWrapper\Command\CommandOptions;

/**
 * Class CallbackqueryCommand
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\CollectionService
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\SettingsService
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\SettingsSilentService
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\SettingsPriorityService
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\SettingsVoicesService
 * @uses \RepeatBot\Bot\Service\CommandService\Commands\EmptyCallbackService
 * @uses \RepeatBot\Bot\Service\CommandService\CallbackQueryDirectorFabric
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $command = new CommandService(
            options: new CommandOptions(
            payload: explode('_', $this->getCallbackQuery()->getData()),
            chatId: $this->getCallbackQuery()->getMessage()->getChat()->getId(),
            messageId: $this->getCallbackQuery()->getMessage()->getMessageId(),
            callbackQueryId: intval($this->getCallbackQuery()->getId())
        ), type: 'query'
        );

        return $command->executeCommand($command->makeService());
    }
}
