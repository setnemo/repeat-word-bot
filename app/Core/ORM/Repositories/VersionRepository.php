<?php
declare(strict_types = 1);

namespace RepeatBot\Core\ORM\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use RepeatBot\Core\ORM\Entities\Version;

class VersionRepository extends EntityRepository
{
    /**
     * @return Version
     */
    public function getNewLatestVersion(): Version
    {
        return $this->findOneBy(['used' => 0], ['createdAt' => 'DESC']);
    }
    
    /**
     * @param Version $entity
     *
     * @throws ORMException
     */
    public function applyVersion(Version $entity): void
    {
        $entity->setUsed(1);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}
