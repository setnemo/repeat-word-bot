<?php
declare(strict_types = 1);

namespace RepeatBot\Core\ORM\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use RepeatBot\Core\ORM\Entities\LearnNotification;
use RepeatBot\Core\ORM\ValueObjects\InactiveUser;

class LearnNotificationCollection extends ArrayCollection
{
    private array $usersIds = [];
    
    public function __construct(array $elements = [])
    {
        /** @var LearnNotification $element */
        foreach ($elements as $element) {
            $this->usersIds[] = $element->getUserId();
        }
        parent::__construct($elements);
    }
    
    /**
     * @param InactiveUser $inactiveUser
     *
     * @return bool
     */
    public function hasUser(InactiveUser $inactiveUser): bool
    {
        return in_array($inactiveUser->getUserId(), $this->getUsersIds());
    }
    
    /**
     * @return array
     */
    public function getUsersIds(): array
    {
        return $this->usersIds;
    }
}
