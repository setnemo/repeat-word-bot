<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CallbackQueryDirectorFabric;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class CallbackqueryCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

    /**
     * @var string
     */
    protected $version = '2.0.0';

    /**
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $command = new CommandService(options: new CommandOptions(
            payload: explode('_', $this->getCallbackQuery()->getData()),
            chatId: $this->getCallbackQuery()->getMessage()->getChat()->getId(),
            messageId: $this->getCallbackQuery()->getMessage()->getMessageId(),
            callbackQueryId: intval($this->getCallbackQuery()->getId())
        ), type: 'query');

        return $command->executeCommand($command->makeService());
    }
}
