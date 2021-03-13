<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
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
        $command = (new CallbackQueryDirectorFabric(
            new CommandOptions(
                payload: explode('_', $this->getCallbackQuery()->getData()),
                chatId: $this->getCallbackQuery()->getMessage()->getChat()->getId(),
                messageId: $this->getCallbackQuery()->getMessage()->getMessageId(),
                callbackQueryId: intval($this->getCallbackQuery()->getId())
            )
        ))->getCommandDirector();

        $service = $command->makeService();

        if (!$service->hasResponse()) {
            $service = $service->execute();
        }

        return $service->postStackMessages()->getResponseMessage();
    }
}
