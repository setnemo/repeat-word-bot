<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Repositories;

use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Core\ORM\Collections\ExportCollection;
use RepeatBot\Core\ORM\Entities\Export;

class ExportRepository extends EntityRepository
{
    /**
     * @param int $userId
     *
     * @return Export
     */
    public function getExport(int $userId): Export
    {
        return $this->findOneBy(['userId' => $userId], ['createdAt' => 'DESC']);
    }

    /**
     * @param Export $entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function applyExport(Export $entity): void
    {
        $entity->setUsed(1);
        $entity->setUpdatedAt(Carbon::now('Europe/Kiev'));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int    $userId
     * @param int    $chatId
     * @param string $wordType
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(int $userId, int $chatId, string $wordType): void
    {
        $entity = new Export();
        $entity->setUserId($userId);
        $entity->setChatId($chatId);
        $entity->setWordType($wordType);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @return ExportCollection
     */
    public function getExports(): ExportCollection
    {
        return new ExportCollection($this->findBy(['used' => 0]));
    }

    /**
     * @param int $userId
     *
     * @return bool
     */
    public function userHaveExport(int $userId): bool
    {
        return $this->findOneBy(['used' => 0, 'userId' => $userId]) !== null;
    }
}
