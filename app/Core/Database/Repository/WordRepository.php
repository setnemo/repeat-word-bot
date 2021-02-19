<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Repository;

use FaaPz\PDO\Clause\Conditional;
use FaaPz\PDO\Clause\Limit;
use RepeatBot\Core\Database\BaseRepository;
use RepeatBot\Core\Database\Model\Word;

/**
 * Class WordRepository
 * @package RepeatBot\Core\Database\Repository
 */
class WordRepository extends BaseRepository
{
    protected string $tableName = 'word';

    /**
     * @param array $params
     *
     * @return Word
     */
    public function getNewModel(array $params): Word
    {
        return new Word($params);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getWordsByCollectionId(int $id): array
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Conditional('collection_id', '=', $id)
            );
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        $ret = [];
        foreach ($result as $record) {
            $ret[] = $this->getNewModel($record);
        }
        return $ret;
    }
    
    public function getWords()
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)->limit(new Limit(20000));
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        $ret = [];
        foreach ($result as $record) {
            $ret[] = $this->getNewModel($record);
        }
        return $ret;
    }
}
