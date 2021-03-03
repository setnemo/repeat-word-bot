<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Repository;

use Carbon\Carbon;
use FaaPz\PDO\Clause\Conditional;
use FaaPz\PDO\Clause\Grouping;
use FaaPz\PDO\Clause\Join;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\Database\BaseRepository;
use RepeatBot\Core\Database\Model\InactiveUser;
use RepeatBot\Core\Database\Model\Training;
use RepeatBot\Core\Exception\EmptyVocabularyException;

/**
 * Class TrainingRepository
 * @package RepeatBot\Core\Database\Repository
 */
class TrainingRepository extends BaseRepository
{
    public const ALWAYS_SILENT_MESSAGE = 1;

    protected string $tableName = 'training';

    /**
     * @param array $params
     *
     * @return Training
     */
    public function getNewModel(array $params): Training
    {
        return new Training($params);
    }

    /**
     * @param int $collection_id
     * @param int $user_id
     *
     * @return bool
     */
    public function userHaveCollection(int $collection_id, int $user_id): bool
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Grouping(
                    "AND",
                    new Conditional("$this->tableName.collection_id", '=', $collection_id),
                    new Conditional("$this->tableName.user_id", '=', $user_id)
                )
            );
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();

        return !empty($result);
    }

    /**
     * @param int         $wordId
     * @param int         $userId
     * @param int         $collection_id
     * @param string      $type
     * @param string      $word
     * @param string      $translate
     * @param string      $voice
     * @param string|null $status
     * @param string|null $repeat
     */
    public function createTraining(
        int $wordId,
        int $userId,
        int $collection_id,
        string $type,
        string $word,
        string $translate,
        string $voice,
        string $status = null,
        string $repeat = null
    ): void {
        $insertStatement = $this->getConnection()->insert(
            [
                'word_id' => $wordId,
                'user_id' => $userId,
                'type' => $type,
                'collection_id' => $collection_id,
                'word' => $word,
                'translate' => $translate,
                'voice' => $voice,
                'status' => $status ?? 'first',
                '`repeat`' => $repeat ?? Carbon::now('Europe/Kiev')->rawFormat('Y-m-d H:i:s'),
            ]
        )->into($this->tableName);
        $r = $insertStatement->execute();
    }

    /**
     * @param int    $userId
     * @param string $type
     * @param bool   $priority
     *
     * @return Training
     * @throws EmptyVocabularyException
     */
    public function getRandomTraining(int $userId, string $type, bool $priority): Training
    {
        $wordTable = 'word';
        $selectStatement = $this->getConnection()->select([
            "$this->tableName.id",
            "$this->tableName.word_id",
            "$this->tableName.user_id",
            "$this->tableName.collection_id",
            "$this->tableName.type",
            "$this->tableName.word",
            "$this->tableName.translate",
            "$wordTable.voice",
            "$this->tableName.status",
            "$this->tableName.repeat",
            "$this->tableName.created_at",
            "$this->tableName.updated_at",
        ])->from($this->tableName)
            ->join(new Join(
                "$wordTable as word",
                new Conditional("{$wordTable}.id", '=', "training.word_id"))
            )
            ->where(
                new Grouping(
                    "AND",
                    new Conditional("$this->tableName.user_id", '=', $userId),
                    new Conditional("$this->tableName.type", '=', $type),
                    new Conditional("$this->tableName.`repeat`", '<', Carbon::now('Europe/Kiev')->rawFormat('Y-m-d H:i:s')),
                    )
            )
        ;
        $stmt = $selectStatement->execute();var_export($stmt->debugDumpParams());
        $result = $stmt->fetchAll();
        var_export(count($result));
        if (!$priority) {
            shuffle($result);
            $ret = $result;
        } else {
            $rule = [
                'first' => 0,
                'second' => 1,
                'third' => 2,
                'fourth' => 3,
                'fifth' => 4,
                'sixth' => 5,
                'never' => 6,
            ];
            $ret2 = [];
            foreach ($result as $item) {
                $ret2[$rule[$item['status']]][] = $item;
            }
            $ret = [];
            for ($i = 0; $i < count($ret2); ++$i) {
                if (!empty($ret)) {
                    break ;
                }
                if (!empty($ret2[$i])) {
                    foreach ($ret2[$i] as $v) {
                        $ret[] =  $v;
                    }
                }
            }
            shuffle($ret);
        }

        if (empty($ret)) {
            throw new EmptyVocabularyException();
        }

        return $this->getNewModel($ret[0]);
    }

    /**
     * @param int $trainingId
     *
     * @return Training
     */
    public function getTraining(int $trainingId): Training
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Conditional('id', '=', $trainingId)
            );
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();

        return $this->getNewModel($result[0]);
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return array
     */
    public function getTrainings(int $userId, string $type): array
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Grouping(
                    "AND",
                    new Conditional("$this->tableName.user_id", '=', $userId),
                    new Conditional("$this->tableName.type", '=', $type)
                )
            );
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        $ret = [];
        foreach ($result as $record) {
            $ret[] = $this->getNewModel($record);
        }

        return $ret;
    }

    public function upStatusTraining(Training $training, bool $never = false): void
    {
        $newStatus = $this->getNewStatus($training, $never);
        $updateStatement = $this->getConnection()->update([
            'status' => $newStatus['status'],
            '`repeat`' => Carbon::now('Europe/Kiev')->addMinutes($newStatus['repeat'])->rawFormat('Y-m-d H:i:s'),
            '`updated_at`' => Carbon::now('Europe/Kiev')->rawFormat('Y-m-d H:i:s'),
        ])->table($this->tableName)
            ->where(
                new Grouping(
                    'AND',
                    new Conditional('id', '=', $training->getId()),
                    new Conditional('type', '=', $training->getType()),
                )
            );
        $affectedRows = $updateStatement->execute();
    }

    /**
     * @param Training $training
     * @param bool     $never
     *
     * @return array
     */
    private function getNewStatus(Training $training, bool $never = false): array
    {
        $status = $never === false ? $training->getStatus() : 'never';

        return match($status) {
            'second' => [
                'status' => 'third',
                'repeat' => 3 * 24 * 60,
            ],
            'third' => [
                'status' => 'fourth',
                'repeat' => 7 * 24 * 60,
            ],
            'fourth' => [
                'status' => 'fifth',
                'repeat' => 30 * 24 * 60,
            ],
            'fifth' => [
                'status' => 'sixth',
                'repeat' => 90 * 24 * 60,
            ],
            'sixth' => [
                'status' => 'never',
                'repeat' => 180 * 24 * 60,
            ],
            'never' => [
                'status' => 'never',
                'repeat' => 360 * 24 * 60,
            ],
            default => [
                'status' => 'second',
                'repeat' => 24 * 60,
            ],
            };
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    public function getMyStats(int $userId): array
    {
        $result = [];
        $types = BotHelper::getTrainingTypes();
        foreach ($types as $type) {
            $selectStatement = $this->getConnection()->select([
                "COUNT(*) as counter, status",
            ])
                ->from($this->tableName)
                ->where(
                    new Grouping(
                        'AND',
                        new Conditional('user_id', '=', $userId),
                        new Conditional('type', '=', $type),
                    )
                )->groupBy("$this->tableName.status");
            $stmt = $selectStatement->execute();
            $resultType = $stmt->fetchAll();
            $result[$type] = $resultType;
        }

        return $result;
    }

    /**
     * @param int $userId
     */
    public function removeAllTrainings(int $userId): void
    {
        $deleteStatement = $this->getConnection()->delete()
            ->from($this->tableName)
            ->where(new Conditional("user_id", "=", $userId));

        $affectedRows = $deleteStatement->execute();
    }

    /**
     * @param int $userId
     */
    public function removeTrainings(int $userId, int $collection_id): void
    {
        $deleteStatement = $this->getConnection()->delete()
            ->from($this->tableName)
            ->where(
                new Grouping(
                    'AND',
                    new Conditional('user_id', '=', $userId),
                    new Conditional('collection_id', '=', $collection_id),
                )
            );

        $affectedRows = $deleteStatement->execute();
    }

    /**
     * @param int $userId
     */
    public function resetTrainings(int $userId, int $collection_id): void
    {
        $updateStatement = $this->getConnection()->update([
            'status' => 'first',
            '`repeat`' => Carbon::now('Europe/Kiev')->rawFormat('Y-m-d H:i:s'),
            '`updated_at`' => Carbon::now('Europe/Kiev')->rawFormat('Y-m-d H:i:s'),
        ])->table($this->tableName)
            ->where(
                new Grouping(
                    'AND',
                    new Conditional('user_id', '=', $userId),
                    new Conditional('collection_id', '=', $collection_id),
                )
            );
        $affectedRows = $updateStatement->execute();
    }

    /**
     * @param int $userId
     */
    public function resetAllTrainings(int $userId): void
    {
        $updateStatement = $this->getConnection()->update([
            'status' => 'first',
            '`repeat`' => Carbon::now('Europe/Kiev')->rawFormat('Y-m-d H:i:s'),
            '`updated_at`' => Carbon::now('Europe/Kiev')->rawFormat('Y-m-d H:i:s'),
        ])->table($this->tableName)
            ->where(new Conditional("user_id", "=", $userId));
        $affectedRows = $updateStatement->execute();
    }

    /**
     * @param array $userNotifications
     *
     * @return array
     */
    public function getInactiveUsers(array $userNotifications): array
    {
        $selectStatement = $this->getConnection()
            ->select([
                "$this->tableName.user_id",
                "MAX($this->tableName.updated_at) as max",
            ])
            ->from($this->tableName)
            ->groupBy("$this->tableName.user_id");
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        $ret = [];
        foreach ($result as $record) {
            if (strtotime($record['max']) < strtotime('-1 day')) {
                if (isset($userNotifications[$record['user_id']])) {
                    if ($userNotifications[$record['user_id']]->getDeleted() == 1) {
                        continue;
                    }
                }
                $ret[$record['user_id']] = $this->getInactiveUser([
                    'user_id' => $record['user_id'],
                    'silent' => isset($userNotifications[$record['user_id']]) ?
                        $userNotifications[$record['user_id']]->getSilent() :
                        self::ALWAYS_SILENT_MESSAGE,
                    'message' => $this->getMessageForInactiveUser($record['user_id']),
                ]);
            }
        }

        return $ret;
    }

    /**
     * @param array $params
     *
     * @return InactiveUser
     */
    private function getInactiveUser(array $params): InactiveUser
    {
        return new InactiveUser($params);
    }

    /**
     * @param int $userId
     *
     * @return string
     */
    private function getMessageForInactiveUser(int $userId): string
    {
        $records = $this->getMyStats($userId);
        $text = "У тебя внушительная стастика:\n";
        foreach ($records as $type => $items) {
            foreach ($items as $item) {
                $status = ucfirst($item['status']);
                $text .= "[{$type}] {$status} итерация: {$item['counter']} слов\n";
            }
        }
        $text .= "\n Не останавливайся! Продолжи свою тренировку прямо сейчас!";

        return $text;
    }

    /**
     * @param int $userId
     *
     * @return Training
     * @throws EmptyVocabularyException
     */
    public function getNearestAvailableTrainingTime(int $userId, string $type): Training
    {
        $selectStatement = $this->getConnection()->select([
            "$this->tableName.*",
        ])->from($this->tableName)
            ->where(
                new Grouping(
                    'AND',
                    new Conditional("$this->tableName.type", '=', $type),
                    new Conditional("$this->tableName.user_id", '=', $userId)
                )
            )->orderBy('`repeat`', 'asc');
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        if (empty($result)) {
            throw new EmptyVocabularyException();
        }

        return $this->getNewModel($result[0]);
    }

    /**
     * @param int    $userId
     * @param int    $type
     * @param string $status
     *
     * @return array
     */
    public function getTrainingsWithStatus(int $userId, string $type, string $status): array
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Grouping(
                    "AND",
                    new Conditional("$this->tableName.status", '=', $status),
                    new Grouping(
                        "AND",
                        new Conditional("$this->tableName.user_id", '=', $userId),
                        new Conditional("$this->tableName.type", '=', $type)
                    )
                )
            );
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        $ret = [];
        foreach ($result as $record) {
            $ret[] = $this->getNewModel($record);
        }

        return $ret;
    }
}
