<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\Commands\WordService;
use TelegramBot\CommandWrapper\Command\CommandOptions;

/**
 * Class UpdateWordCommand
 * @uses WordService
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class WordCommand extends SystemCommand
{
    protected $usage = '/word';

    /**
     * Command execute method
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $textFromInput = BotHelper::getTextFromInput($this->getMessage()->getText(true));
        $explode       = explode(' ', $textFromInput);
        $first         = array_shift($explode);
        $command       = new CommandService(
            options: new CommandOptions(
                command: 'word',
                payload: [
                    WordService::CMD  => $first,
                    WordService::BODY => implode(' ', $explode),
                ],
                chatId: $this->getMessage()->getChat()->getId(),
            )
        );

        return $command->executeCommand($command->makeService());
    }
}
