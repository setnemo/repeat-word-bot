<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Repositories;

use Doctrine\ORM\EntityRepository;
use RepeatBot\Core\ORM\Entities\Collection;

/**
 * Class CollectionRepository
 * @package RepeatBot\Core\ORM\Repositories
 */
class CollectionRepository extends EntityRepository
{
    /**
     * @param int $id
     *
     * @return Collection
     */
    public function getCollection(int $id): Collection
    {
        return $this->findOneBy(['id' => $id]);
    }
}
