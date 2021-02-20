<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Repository;

use Carbon\Carbon;
use FaaPz\PDO\Clause\Conditional;
use FaaPz\PDO\Clause\Grouping;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\App;
use RepeatBot\Core\Database\BaseRepository;
use RepeatBot\Core\Database\Model\InactiveUser;
use RepeatBot\Core\Database\Model\Training;
use RepeatBot\Core\Database\Model\Word;
use RepeatBot\Core\Exception\EmptyVocabularyException;
use RepeatBot\Core\Log;

/**
 * Class TrainingRepository
 * @package RepeatBot\Core\Database\Repository
 */
class TrainingRepository extends BaseRepository
{
    const ALWAYS_SILENT_MESSAGE = 1;

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
    public function userHaveCollectionId(int $collection_id, int $user_id): bool
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

    public function addNewWords(array $words, int $userId): int
    {
        $i = 0;
        $config = App::getInstance()->getConfig();
        $logger = Log::getInstance()->init($config)->getLogger();
        foreach (BotHelper::getTrainingTypes() as $type) {
            /** @var Word $word */
            foreach ($words as $word) {
                try {
                    $this->createTraining(
                        $word->getId(),
                        $userId,
                        $word->getCollectionId(),
                        $type,
                        $word->getWord(),
                        $word->getTranslate(),
                        $word->getVoice()
                    );
                    ++$i;
                } catch (\Throwable $t) {
                    $logger->error('addNewWords: ' . $t->getMessage(), $t->getTrace());
                }
            }
        }

        return $i;
    }

    /**
     * @param int    $wordId
     * @param int    $userId
     * @param int    $collectionId
     * @param string $type
     * @param string $word
     * @param string $translate
     * @param string $voice
     */
    public function createTraining(
        int $wordId,
        int $userId,
        int $collectionId,
        string $type,
        string $word,
        string $translate,
        string $voice
    ): void {
        $insertStatement = $this->getConnection()->insert(
            [
                'word_id'       => $wordId,
                'user_id'       => $userId,
                'type'          => $type,
                'collection_id' => $collectionId,
                'word'          => $word,
                'translate'     => $translate,
                'voice'         => $voice,
            ]
        )->into($this->tableName);
        $insertStatement->execute();
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return Training
     * @throws EmptyVocabularyException
     */
    public function getRandomTraining(int $userId, string $type): Training
    {
        $selectStatement = $this->getConnection()->select([
            "$this->tableName.*"
        ])->from($this->tableName)
            ->where(
                new Grouping(
                    "AND",
                    new Conditional("$this->tableName.repeat", '<', Carbon::now('UTC')->rawFormat('Y-m-d H:i:s')),
                    new Grouping(
                        "AND",
                        new Conditional("$this->tableName.user_id", '=', $userId),
                        new Conditional("$this->tableName.type", '=', $type)
                    )
                )
            )
        ;
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        shuffle($result);

        if (empty($result)) {
            throw new EmptyVocabularyException();
        }
        return $this->getNewModel($result[0]);
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

    public function upStatusTraining(Training $training): void
    {
        $newStatus = $this->getNewStatus($training);
        $updateStatement = $this->getConnection()->update([
            'status' => $newStatus['status'],
            '`repeat`' => Carbon::now()->addMinutes($newStatus['repeat'])->rawFormat('Y-m-d H:i:s'),
            '`updated_at`' => Carbon::now()->rawFormat('Y-m-d H:i:s'),
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
     *
     * @return array
     */
    private function getNewStatus(Training $training): array
    {
        return match($training->getStatus()) {
             'first' => [
                    'status' => 'second',
                    'repeat' => 24 * 60
             ],
             'second' => [
                    'status' => 'third',
                    'repeat' => 3 * 24 * 60
             ],
             'third' => [
                    'status' => 'fourth',
                    'repeat' => 7 * 24 * 60
             ],
             'fourth' => [
                    'status' => 'fifth',
                    'repeat' => 30 * 24 * 60
             ],
             'fifth' => [
                    'status' => 'sixth',
                    'repeat' => 90 * 24 * 60
             ],
             'sixth' => [
                    'status' => 'never',
                    'repeat' => 180 * 24 * 60
             ],
             'never' => [
                    'status' => 'never',
                    'repeat' => 360 * 24 * 60
             ],
             default => 'first',
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
                "COUNT(*) as counter, status"
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
    public function resetTrainings(int $userId): void
    {
        $deleteStatement = $this->getConnection()->delete()
            ->from($this->tableName)
            ->where(new Conditional("user_id", "=", $userId));

        $affectedRows = $deleteStatement->execute();
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
                "MAX($this->tableName.updated_at) as max"
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
                        continue ;
                    }
                }
                $ret[$record['user_id']] = $this->getInactiveUser([
                    'user_id' => $record['user_id'],
                    'silent' => isset($userNotifications[$record['user_id']]) ?
                        $userNotifications[$record['user_id']]->getSilent() :
                        self::ALWAYS_SILENT_MESSAGE,
                    'message' => $this->getMessageForInactiveUser($record['user_id'])
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
}
