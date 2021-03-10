<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use RepeatBot\Bot\Service\CommandService\CommandDirector;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class ProgressCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class ProgressCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'progress';
    /**
     * @var string
     */
    protected $description = 'progress command';
    /**
     * @var string
     */
    protected $usage = '/progress';
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
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $command = new CommandDirector(
            new CommandOptions(
                'progress',
                [],
                $this->getMessage()->getChat()->getId()
            )
        );
        $service = $command->makeService();

        if (!$service->hasResponse()) {
            $service = $service->execute();
        }

        return $service->postStackMessages()->getResponseMessage();
    }
}
