<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\CommandDirector;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class TrainingCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class TrainingCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'Training';
    /**
     * @var string
     */
    protected $description = 'Training command';
    /**
     * @var string
     */
    protected $usage = '/training';
    /**
     * @var string
     */
    protected $version = '1.0.0';
    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $director = new CommandDirector(
            new CommandOptions(
                command: 'training',
                chatId: $this->getMessage()->getChat()->getId()
            )
        );
        $service = $director->makeService();

        if (!$service->hasResponse()) {
            $service = $service->execute();
        }

        return $service->postStackMessages()->getResponseMessage();
    }
}
