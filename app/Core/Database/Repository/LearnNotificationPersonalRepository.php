<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Repository;

use Carbon\Carbon;
use FaaPz\PDO\Clause\Conditional;
use FaaPz\PDO\Clause\Limit;
use RepeatBot\Core\Database\BaseRepository;
use RepeatBot\Core\Database\Model\LearnNotificationPersonal;

/**
 * Class LearnNotificationPersonalRepository
 * @package RepeatBot\Core\Database\Repository
 */
class LearnNotificationPersonalRepository extends BaseRepository
{
    protected string $tableName = 'learn_notification_personal';

    /**
     * @param array $params
     *
     * @return LearnNotificationPersonal
     */
    public function getNewModel(array $params): LearnNotificationPersonal
    {
        return new LearnNotificationPersonal($params);
    }

    /**
     * @return array
     */
    public function getNotifications(): array
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Conditional(
                    'updated_at',
                    '<',
                    Carbon::now('Europe/Kiev')->subDay()->rawFormat('Y-m-d H:i:s')
                )
            )
            ->orderBy('created_at', 'desc');
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        $ret = [];
        foreach ($result as $record) {
            $ret[] = $this->getNewModel($record);
        }
        return $ret;
    }

    /**
     * @param int    $userId
     * @param string $message
     * @param string $alarm
     * @param string $tz
     */
    public function createNotification(int $userId, string $message, string $alarm, string $tz): void
    {
        $insertStatement = $this->getConnection()->insert(
            [
                'user_id'       => $userId,
                'message'       => $message,
                'alarm'         => $alarm,
                'timezone'      => $tz,
            ]
        )->into($this->tableName);
        $insertStatement->execute();
    }

    /**
     * @param int $user_id
     *
     * @return array
     */
    public function getMyAlarms(int $user_id): array
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Conditional('user_id', '=', $user_id)
            )
            ->orderBy('created_at', 'desc');
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        $ret = [];
        foreach ($result as $record) {
            $ret[] = $this->getNewModel($record);
        }
        return $ret;
    }

    /**
     * @param int $user_id
     */
    public function delNotifications(int $user_id): void
    {
        $deleteStatement = $this->getConnection()->delete()
            ->from($this->tableName)
            ->where(new Conditional("user_id", "=", $user_id));

        $affectedRows = $deleteStatement->execute();
    }

    public function updateNotification(LearnNotificationPersonal $notification)
    {
        $updateStatement = $this->getConnection()->update([
            '`updated_at`' => Carbon::now('Europe/Kiev')->rawFormat('Y-m-d H:i:s'),
        ])->table($this->tableName)
            ->where(new Conditional('id', '=', $notification->getId()));
        $affectedRows = $updateStatement->execute();
    }
}
