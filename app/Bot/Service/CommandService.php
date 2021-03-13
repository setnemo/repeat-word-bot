<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service;

use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\Service\CommandService\CallbackQueryDirectorFabric;
use RepeatBot\Bot\Service\CommandService\CommandDirector;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Commands\CommandInterface;
use RepeatBot\Bot\Service\CommandService\GenericMessageDirectorFabric;

/**
 * Class CommandService
 * @package RepeatBot\Bot\Service\CommandService
 */
class CommandService
{
    /**
     * @param string         $type
     * @param CommandOptions $options
     *
     * @return ServerResponse
     */
public function execute(CommandOptions $options, string $type = ''): ServerResponse
{
    $service = match ($type) {
        'query' => $this->makeQueryService($options),
        'generic' => $this->makeGenericService($options),
    default => $this->makeService($options),
    };

        return $this->makeCommand($service);
        }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeService(CommandOptions $options): CommandInterface
    {
        $command = new CommandDirector($options);

        return $command->makeService();
    }

    /**
     * @param CommandInterface $service
     *
     * @return ServerResponse
     */
    private function makeCommand(CommandInterface $service): ServerResponse
    {
        if (!$service->hasResponse()) {
            $service = $service->execute();
        }

        return $service->postStackMessages()->getResponseMessage();
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeQueryService(CommandOptions $options): CommandInterface
    {
        $command = (new CallbackQueryDirectorFabric($options))->getCommandDirector();

        return $command->makeService();
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeGenericService(CommandOptions $options): CommandInterface
    {
        $command = (new GenericMessageDirectorFabric($options))->getCommandDirector();

        return $command->makeService();
    }
}
