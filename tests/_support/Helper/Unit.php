<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Core\Cache as CoreCache;

class Unit extends \Codeception\Module
{
    public function addCollection(int $userId, int $num = 1): void
    {
        $command = new CommandService(options: new CommandOptions(
            payload: explode('_', 'collections_add_' . $num),
            chatId: $userId,
        ), type: 'query');

        $command->makeService()->execute();
    }

    public function getCache(): CoreCache
    {
        return Cache::getCache();
    }
}
