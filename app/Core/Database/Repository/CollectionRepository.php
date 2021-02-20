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
     * @return array
     */
    public function getAllPublicCollection(): array
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Conditional('public', '=', 1)
            );
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        $ret = [];
        foreach ($result as $record) {
            $ret[$record['id']] = $this->getNewModel($record);
        }
        return $ret;
    }
}
