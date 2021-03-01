<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Repository;

use Carbon\Carbon;
use FaaPz\PDO\Clause\Conditional;
use RepeatBot\Core\Database\BaseRepository;
use RepeatBot\Core\Database\Model\LearnNotification;
use RepeatBot\Core\Database\Model\UserNotification;

/**
 * Class UserNotificationRepository
 * @package RepeatBot\Core\Database\Repository
 */
class UserNotificationRepository extends BaseRepository
{
    const ALWAYS_SILENT_MESSAGE = 1;

    protected string $tableName = 'user_notification';

    /**
     * @param array $params
     *
     * @return UserNotification
     */
    public function getNewModel(array $params): UserNotification
    {
        return new UserNotification($params);
    }

    /**
     * @return array
     */
    public function getUserNotifications(): array
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName);
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        $ret = [];
        foreach ($result as $record) {
            $model = $this->getNewModel($record);
            $ret[$model->getUserId()] = $model;
        }
        return $ret;
    }

    /**
     * @param int $id
     */
    public function deleteUserNotification(int $id): void
    {
        $this->getOrCreateUserNotification($id);
        $updateStatement = $this->getConnection()->update([
            'deleted' => 1,
            'deleted_at' => Carbon::now('Europe/Kiev')->rawFormat('Y-m-d H:i:s'),
        ])
            ->table($this->tableName)
            ->where(new Conditional('user_id', '=', $id));
        $affectedRows = $updateStatement->execute();
    }

    public function getOrCreateUserNotification(int $user_id): UserNotification
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(new Conditional('user_id', '=', $user_id));
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        if (empty($result)) {
            $model = $this->getNewModel([
                'user_id' => $user_id,
                'silent' => self::ALWAYS_SILENT_MESSAGE,
                'deleted' => 0,
            ]);
            $this->createNotification($user_id, self::ALWAYS_SILENT_MESSAGE);

            return $model;
        }

        return $this->getNewModel($result[0]);
    }

    public function updateUserNotification(int $user_id, int $silent, int $deleted = 0): void
    {
        $updateStatement = $this->getConnection()->update(['silent' => $silent, 'deleted' => $deleted])
            ->table($this->tableName)
            ->where(new Conditional('user_id', '=', $user_id));
        $affectedRows = $updateStatement->execute();
    }

    public function createNotification(int $user_id, int $silent, int $deleted = 0): void
    {
        $insertStatement = $this->getConnection()->insert([
            'user_id' => $user_id,
            'silent' => $silent,
            'deleted' => $deleted
        ])->into($this->tableName);
        $insertStatement->execute();
    }

    /**
     * @param int $user_id
     * @param int $silent
     * @param int $deleted
     */
    public function createOdUpdateNotification(int $user_id, int $silent, int $deleted = 0): void
    {
        $this->getOrCreateUserNotification($user_id);
        $this->updateUserNotification($user_id, $silent, $deleted);
    }
}
