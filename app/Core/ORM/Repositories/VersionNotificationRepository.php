<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use RepeatBot\Core\ORM\Entities\VersionNotification;

/**
 * Class VersionNotificationRepository
 * @package RepeatBot\Core\ORM\Repositories
 */
class VersionNotificationRepository extends EntityRepository
{
    /**
     * @param VersionNotification $entity
     *
     * @throws ORMException
     */
    public function saveVersionNotification(VersionNotification $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}
