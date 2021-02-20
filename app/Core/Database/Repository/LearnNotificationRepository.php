<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Repository;

use Carbon\Carbon;
use FaaPz\PDO\Clause\Conditional;
use FaaPz\PDO\Clause\Limit;
use RepeatBot\Core\Database\BaseRepository;
use RepeatBot\Core\Database\Model\InactiveUser;
use RepeatBot\Core\Database\Model\LearnNotification;

/**
 * Class LearnNotificationRepository
 * @package RepeatBot\Core\Database\Repository
 */
class LearnNotificationRepository extends BaseRepository
{
    protected string $tableName = 'learn_notification';

    /**
     * @param array $params
     *
     * @return LearnNotification
     */
    public function getNewModel(array $params): LearnNotification
    {
        return new LearnNotification($params);
    }

    /**
     * @return array
     */
    public function getUnsentNotifications(): array
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Conditional('used', '=', 0)
            )
            ->orderBy('created_at', 'desc')
            ->limit(new Limit(10));
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
     * @param int    $silent
     */
    public function createNotification(int $userId, string $message, int $silent): void
    {
        $insertStatement = $this->getConnection()->insert(
            [
                'user_id'       => $userId,
                'message'       => $message,
                'silent'        => $silent,
            ]
        )->into($this->tableName);
        $insertStatement->execute();
    }

    /**
     * @param LearnNotification $notification
     */
    public function updateNotification(LearnNotification $notification): void
    {
        $updateStatement = $this->getConnection()->update([
            'used' => 1,
            '`updated_at`' => Carbon::now()->rawFormat('Y-m-d H:i:s'),
        ])->table($this->tableName)
            ->where(new Conditional('id', '=', $notification->getId()));
        $affectedRows = $updateStatement->execute();
    }

    /**
     * @param array $inactiveUsers
     *
     * @return array
     */
    public function filterNotifications(array $inactiveUsers): array
    {
        $selectStatement = $this->getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Conditional(
                    'created_at',
                    '>',
                    Carbon::now('UTC')->subDays()->addMinutes()->rawFormat('Y-m-d H:i:s')
                )
            );
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll();
        $filter = [];

        foreach ($result as $record) {
            $filter[] = $record['user_id'];
        }
        $newNotifications = [];
        /** @var InactiveUser $inactiveUser */
        foreach ($inactiveUsers as $inactiveUser) {
            if (!in_array($inactiveUser->getUserId(), $filter)) {
                $newNotifications[] = $this->getNewModel([
                    'user_id' => $inactiveUser->getUserId(),
                    'message' => $inactiveUser->getMessage(),
                    'silent' => $inactiveUser->getSilent(),
                ]);
            }
        }

        return $newNotifications;
    }

    /**
     * @param array $newNotifications
     */
    public function saveNotifications(array $newNotifications): void
    {

        /** @var LearnNotification $notification */
        foreach ($newNotifications as $notification) {
            $this->createNotification(
                $notification->getUserId(),
                $notification->getMessage(),
                $notification->getSilent(),
            );
        }
    }
}
