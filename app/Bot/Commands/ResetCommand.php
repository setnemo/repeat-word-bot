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
 * Class ResetCommand
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
        return (new CommandService())->execute(
            options: new CommandOptions(
                command: 'reset',
                payload: explode(' ', BotHelper::getTextFromInput($this->getMessage()->getText(true))),
                chatId: $this->getMessage()->getChat()->getId(),
            )
        );
    }
}
