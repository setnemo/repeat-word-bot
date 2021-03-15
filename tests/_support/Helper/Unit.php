<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Carbon\Carbon;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Core\Cache as CoreCache;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\Training;

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

    public function updateAllTrainingStatuses($userId, $type)
    {
        $em = ORM::getEntityManager();
        $trainings = $em->getRepository(Training::class)->getTrainings($userId, $type);
        foreach ($trainings as $training) {
            $training->setStatus('third');
            $training->setNext(Carbon::now(Database::DEFAULT_TZ)->addMinutes(3 * 24 * 60));
            $training->setUpdatedAt(Carbon::now(Database::DEFAULT_TZ));
            $em->persist($training);
        }
        $em->flush();
    }
}
