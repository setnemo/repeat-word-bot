<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Repository;

use FaaPz\PDO\Clause\Conditional;
use RepeatBot\Core\Database\BaseRepository;
use RepeatBot\Core\Database\Model\Rating;

/**
 * Class RatingRepository
 * @package RepeatBot\Core\Database\Repository
 */
class RatingRepository extends BaseRepository
{
    protected string $tableName = 'rating';

    /**
     * @param array $params
     *
     * @return Rating
     */
    public function getNewModel(array $params): Rating
    {
        return new Rating($params);
    }

    /**
     * @return array
     */
    public function getAllPublicRating(): array
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

    /**
     * @param int $id
     *
     * @return Rating
     */
    public function getCollection(int $id): Rating
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
