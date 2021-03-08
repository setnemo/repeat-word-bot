<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use RepeatBot\Core\ORM\Entities\LearnNotification;
use RepeatBot\Core\ORM\ValueObjects\InactiveUser;

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
            $learnNotificationCollection->add($learnNotification);
        }

        return $learnNotificationCollection;
    }
}
