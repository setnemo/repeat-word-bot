<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Collections;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\LearnNotification;
use RepeatBot\Core\ORM\ValueObjects\InactiveUser;

/**
 * Class InactiveUserCollection
 * @package RepeatBot\Core\ORM\Collections
 */
class InactiveUserCollection extends ArrayCollection
{
    public function convertToLearnNotification(): LearnNotificationCollection
    {
        $learnNotificationCollection = new LearnNotificationCollection();
        /** @var InactiveUser $inactiveUser */
        foreach ($this->getValues() as $inactiveUser) {
            $learnNotification = new LearnNotification();
            $learnNotification->setUserId($inactiveUser->getUserId());
            $learnNotification->setMessage($inactiveUser->getMessage());
            $learnNotification->setSilent($inactiveUser->getSilent());
            $learnNotification->setCreatedAt(Carbon::now(Database::DEFAULT_TZ));
            $learnNotificationCollection->add($learnNotification);
        }

        return $learnNotificationCollection;
    }
}
