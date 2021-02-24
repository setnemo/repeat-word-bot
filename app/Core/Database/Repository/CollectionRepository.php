<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Repository;

use FaaPz\PDO\Clause\Conditional;
use RepeatBot\Core\Database\BaseRepository;
use RepeatBot\Core\Database\Model\Collection;

/**
 * Class CollectionRepository
 * @package RepeatBot\Core\Database\Repository
 */
class CollectionRepository extends BaseRepository
{
    protected string $tableName = 'collection';

    /**
     * @param array $params
     *
     * @return Collection
     */
    public function getNewModel(array $params): Collection
    {
        return new Collection($params);
    }

    /**
     * @param int $id
     *
     * @return Collection
     */
    public function getCollection(int $id): Collection
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Conditional('id', '=', $id)
            );
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        return $this->getNewModel($result[0]);
    }
}
