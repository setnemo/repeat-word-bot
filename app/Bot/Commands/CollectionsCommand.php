<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Prometheus\Exception\MetricsRegistrationException;
use RepeatBot\Bot\Service\CommandService\CommandDirector;
use RepeatBot\Bot\Service\CommandService\CommandOptions;

/**
 * Class CollectionsCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class CollectionsCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'Collections';
    /**
     * @var string
     */
    protected $description = 'Collections command';
    /**
     * @var string
     */
    protected $usage = '/collections';
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
     * @throws MetricsRegistrationException
     */
    public function execute(): ServerResponse
    {
        $director = new CommandDirector(
            new CommandOptions(
                'collection',
                [],
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
