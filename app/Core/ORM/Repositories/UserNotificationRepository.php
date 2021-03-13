<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Repositories;

use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Collections\UserNotificationCollection;
use RepeatBot\Core\ORM\Entities\UserNotification;

/**
 * Class UserNotificationRepository
 * @package RepeatBot\Core\ORM\Repositories
 */
class UserNotificationRepository extends EntityRepository
{
    /**
     * @return UserNotificationCollection
     */
    public function getUserNotifications(): UserNotificationCollection
    {
        return new UserNotificationCollection(
            $this->findBy([])
        );
    }

    /**
     * @param int $userId
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteUserNotification(int $userId): void
    {
        $entity = $this->getOrCreateUserNotification($userId);
        $entity->setDeleted(1);
        $entity->setDeletedAt(Carbon::now(Database::DEFAULT_TZ));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $userId
     *
     * @return UserNotification
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getOrCreateUserNotification(int $userId): UserNotification
    {
        $entity = $this->findOneBy(['userId' => $userId]);

        if (null === $entity) {
            $entity = new UserNotification();
            $entity->setUserId($userId);
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
        }

        return $entity;
    }

    /**
     * @param int $userId
     * @param int $silent
     * @param int $deleted
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateUserNotification(int $userId, int $silent, int $deleted = 0): void
    {
        $entity = $this->getOrCreateUserNotification($userId);
        $entity->setSilent($silent);
        $entity->setDeleted($deleted);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $userId
     * @param int $silent
     * @param int $deleted
     *
     * @return UserNotification
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNotification(int $userId, int $silent, int $deleted = 0): UserNotification
    {
        $entity = new UserNotification();
        $entity->setUserId($userId);
        $entity->setSilent($silent);
        $entity->setDeleted($deleted);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     * @param int $userId
     * @param int $silent
     * @param int $deleted
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createOdUpdateNotification(int $userId, int $silent, int $deleted = 0): void
    {
        $this->getOrCreateUserNotification($userId);
        $this->updateUserNotification($userId, $silent, $deleted);
    }
}
