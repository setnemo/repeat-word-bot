<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Repository;

use Carbon\Carbon;
use FaaPz\PDO\Clause\Conditional;
use FaaPz\PDO\Clause\Grouping;
use RepeatBot\Core\Database\BaseRepository;
use RepeatBot\Core\Database\Model\TrainingSave;

/**
 * Class TrainingSaveRepository
 * @package RepeatBot\Core\Database\Repository
 */
class TrainingSaveRepository extends BaseRepository
{
    protected string $tableName = 'training_save';

    /**
     * @param array $params
     *
     * @return TrainingSave
     */
    public function getNewModel(array $params): TrainingSave
    {
        return new TrainingSave($params);
    }

    /**
     * @param int $user_id
     *
     * @return array
     */
    public function getTrainingSave(int $user_id): array
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Grouping(
                    "AND",
                    new Conditional("$this->tableName.user_id", '=', $user_id),
                    new Conditional("$this->tableName.used", '=', 0)
                )
            );
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        $ret = [];
        foreach ($result as $record) {
            $ret[$record['type']][$record['word']] = $this->getNewModel($record);
        }
        return $ret;
    }

    public function setUsed(TrainingSave $save): void
    {
        $updateStatement = $this->getConnection()->update([
            'used' => 1,
            '`updated_at`' => Carbon::now('Europe/Kiev')->rawFormat('Y-m-d H:i:s'),
        ])->table($this->tableName)
            ->where(new Conditional('id', '=', $save->getId()));
        $affectedRows = $updateStatement->execute();
    }
}
