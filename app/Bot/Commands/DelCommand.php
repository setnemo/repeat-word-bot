<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use RepeatBot\Bot\Service\CommandService\CommandDirector;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class DelCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class DelCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'Del';
    /**
     * @var string
     */
    protected $description = 'Del command';
    /**
     * @var string
     */
    protected $usage = '/del';
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
        $director = new CommandDirector(
            new CommandOptions(
                'del',
                explode(' ', $this->getMessage()->getText(true)),
                $this->getMessage()->getChat()->getId(),
            )
        );
        $service = $director->makeService();

        if (!$service->hasResponse()) {
            $service->execute();
        }

        return $service->postStackMessages()->getResponseMessage();
    }
}
