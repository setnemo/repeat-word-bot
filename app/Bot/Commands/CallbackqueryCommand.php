<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\Service\CommandService\CallbackQueryDirectorFabric;

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
            $this->getCallbackQuery()->getData(),
            $this->getCallbackQuery()->getMessage()->getChat()->getId(),
            $this->getCallbackQuery()->getMessage()->getMessageId(),
            intval($this->getCallbackQuery()->getId())
        ))->getCommandDirector();

        $service = $command->makeService();

        if (!$service->hasResponse()) {
            $service = $service->execute();
        }

        return $service->postStackMessages()->getResponseMessage();
    }
}
