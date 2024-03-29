<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Repositories;

use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Collections\InactiveUserCollection;
use RepeatBot\Core\ORM\Collections\LearnNotificationCollection;
use RepeatBot\Core\ORM\Entities\LearnNotification;
use RepeatBot\Core\ORM\ValueObjects\InactiveUser;

/**
 * Class LearnNotificationRepository
 * @package RepeatBot\Core\ORM\Repositories
 */
class LearnNotificationRepository extends EntityRepository
{
    /**
     * @return LearnNotificationCollection
     */
    public function getUnsentNotifications(): LearnNotificationCollection
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('ln')
            ->from('RepeatBot\Core\ORM\Entities\LearnNotification', 'ln')
            ->where('ln.used = 0')
            ->orderBy('ln.createdAt', 'DESC');
        return new LearnNotificationCollection($query->getQuery()->getResult());
    }

    /**
     * @return void
     */
    public function deleteDeprecatedNotifications(): void
    {
        $this->getEntityManager()->createQueryBuilder()
            ->delete('RepeatBot\Core\ORM\Entities\LearnNotification', 'ln')
            ->where('ln.updatedAt is not null')
            ->andWhere('ln.updatedAt < :updated')
            ->setParameter(
                'updated',
                Carbon::now(Database::DEFAULT_TZ)->subDays()
            )->getQuery()->execute();
    }

    /**
     * @param int $userId
     * @param string $message
     * @param int $silent
     *
     * @return LearnNotification
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNotification(int $userId, string $message, int $silent): LearnNotification
    {
        $entity = new LearnNotification();
        $entity->setUserId($userId);
        $entity->setMessage($message);
        $entity->setSilent($silent);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     * @param LearnNotification $entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateNotification(LearnNotification $entity): void
    {
        $entity->setUsed(1);
        $entity->setUpdatedAt(Carbon::now(Database::DEFAULT_TZ));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param InactiveUserCollection $inactiveUsers
     *
     * @return LearnNotificationCollection
     */
    public function filterNotifications(InactiveUserCollection $inactiveUsers): LearnNotificationCollection
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('ln')
            ->from('RepeatBot\Core\ORM\Entities\LearnNotification', 'ln')
            ->where('ln.createdAt > :created')
            ->setParameter(
                'created',
                Carbon::now(Database::DEFAULT_TZ)->subDays()->addMinutes()
            )->orderBy('ln.createdAt', 'DESC');

        $learnNotificationCollection = new LearnNotificationCollection($query->getQuery()->getResult());
        $inactiveUsers               = $inactiveUsers->filter(
            static function (InactiveUser $current) use ($learnNotificationCollection) {
                return !$learnNotificationCollection->hasUser($current);
            }
        );
        return $inactiveUsers->convertToLearnNotification();
    }

    /**
     * @param LearnNotificationCollection $newNotifications
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveNotifications(LearnNotificationCollection $newNotifications): void
    {
        /** @var LearnNotification $notification */
        foreach ($newNotifications as $notification) {
            $this->getEntityManager()->persist($notification);
            $this->getEntityManager()->flush();
        }
    }
}
