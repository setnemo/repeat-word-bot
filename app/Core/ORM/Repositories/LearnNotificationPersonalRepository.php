<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Repositories;

use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Collections\LearnNotificationPersonalCollection;
use RepeatBot\Core\ORM\Entities\LearnNotificationPersonal;

/**
 * Class LearnNotificationPersonalRepository
 * @package RepeatBot\Core\ORM\Repositories
 */
class LearnNotificationPersonalRepository extends EntityRepository
{
    /**
     * @return LearnNotificationPersonalCollection
     */
    public function getNotifications(): LearnNotificationPersonalCollection
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('lnp')
            ->from('RepeatBot\Core\ORM\Entities\LearnNotificationPersonal', 'lnp')
            ->where('lnp.updatedAt < :upd')
            ->setParameter(
                'upd',
                Carbon::now(Database::DEFAULT_TZ)->subDay()
            );

        return new LearnNotificationPersonalCollection($query->getQuery()->getResult());
    }

    /**
     * @param int    $userId
     * @param string $message
     * @param string $alarm
     * @param string $tz
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNotification(int $userId, string $message, string $alarm, string $tz): void
    {
        $time = explode(':', $alarm);
        array_walk($time, static function ($item) {
            return (int) $item;
        });
        $updated = Carbon::now(Database::DEFAULT_TZ)->setTime($time[0], $time[1], 0);
        if (Carbon::now(Database::DEFAULT_TZ)->lessThan($updated)) {
            $updated = Carbon::now(Database::DEFAULT_TZ)->subDay()->setTime($time[0], $time[1], 0);
        }
        $entity = new LearnNotificationPersonal();
        $entity->setUserId($userId);
        $entity->setMessage($message);
        $entity->setAlarm($updated);
        $entity->setUpdatedAt($updated);
        $entity->setTimezone($tz);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $userId
     *
     * @return LearnNotificationPersonalCollection
     */
    public function getMyAlarms(int $userId): LearnNotificationPersonalCollection
    {
        return new LearnNotificationPersonalCollection(
            $this->findBy(['userId' => $userId], ['createdAt' => 'DESC'])
        );
    }

    /**
     * @param int $userId
     */
    public function delNotifications(int $userId): void
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->delete('RepeatBot\Core\ORM\Entities\LearnNotificationPersonal', 'lnp')
            ->where('lnp.userId = :userId')
            ->setParameter('userId', $userId);

        $query->getQuery()->execute();
    }

    /**
     * @param LearnNotificationPersonal $entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateNotification(LearnNotificationPersonal $entity): void
    {
        $entity->setUpdatedAt(Carbon::now(Database::DEFAULT_TZ));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}
