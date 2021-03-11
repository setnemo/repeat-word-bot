<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\Service\CommandService\GenericMessageDirectorFabric;

class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $text = $this->getMessage()->getText(false) ?? '';
        $command = (new GenericMessageDirectorFabric(
            $text,
            $this->getMessage()->getFrom()->getId(),
        ))->getCommandDirector();
    
        $service = $command->makeService();
    
        if (!$service->hasResponse()) {
            $service = $service->execute();
        }
    
        return $service->postStackMessages()->getResponseMessage();
    }
}
