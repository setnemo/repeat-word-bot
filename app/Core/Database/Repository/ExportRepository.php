<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Repository;

use Carbon\Carbon;
use FaaPz\PDO\Clause\Conditional;
use FaaPz\PDO\Clause\Grouping;
use FaaPz\PDO\Clause\Limit;
use RepeatBot\Core\Database\BaseRepository;
use RepeatBot\Core\Database\Model\Export;

/**
 * Class ExportRepository
 * @package RepeatBot\Core\Database\Repository
 */
class ExportRepository extends BaseRepository
{
    protected string $tableName = 'export';

    /**
     * @param array $params
     *
     * @return Export
     */
    public function getNewModel(array $params): Export
    {
        return new Export($params);
    }

    /**
     * @return Export
     */
    public function getExport(int $userId): Export
    {
        $selectStatement = $this->
        getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Grouping(
                    "AND",
                    new Conditional("$this->tableName.user_id", '=', $userId),
                    new Conditional('used', '=', 0)
                )
            )
            ->limit(new Limit(1));
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll()[0] ?? [];
        return $this->getNewModel($result);
    }

    /**
     * @param Export $export
     */
    public function applyExport(Export $export): void
    {
        $updateStatement = $this->getConnection()
            ->update([
                'used' => 1,
                '`updated_at`' => Carbon::now('Europe/Kiev')->rawFormat('Y-m-d H:i:s'),
            ])->table($this->tableName)
            ->where(new Conditional('id', '=', $export->getId()));
        $affectedRows = $updateStatement->execute();
    }

    public function create(int $userId, int $chatId, string $wordType)
    {
        $insertStatement = $this->getConnection()->insert([
            'user_id' => $userId,
            'chat_id' => $chatId,
            'word_type' => $wordType
        ])->into($this->tableName);
        $insertStatement->execute();
    }

    public function getExports()
    {
        $selectStatement = $this->
        getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Conditional('used', '=', 0)
            );
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        $ret = [];
        foreach ($result as $record) {
            $ret[] = $this->getNewModel($record);
        }

        return $ret;
    }

    public function userHaveExport(int $user_id)
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Grouping(
                    "AND",
                    new Conditional("$this->tableName.used", '=', 0),
                    new Conditional("$this->tableName.user_id", '=', $user_id)
                )
            );
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();

        return !empty($result);
    }
}
