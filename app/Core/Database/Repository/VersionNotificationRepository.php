<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Repository;

use RepeatBot\Core\Database\BaseRepository;
use RepeatBot\Core\Database\Model\VersionNotification;

/**
 * Class VersionNotificationRepository
 * @package RepeatBot\Core\Database\Repository
 */
class VersionNotificationRepository extends BaseRepository
{
    protected string $tableName = 'version_notification';

    /**
     * @param array $params
     *
     * @return VersionNotification
     */
    public function getNewModel(array $params): VersionNotification
    {
        return new VersionNotification($params);
    }

    public function saveVersionNotification(VersionNotification $model)
    {
        $insertStatement = $this->getConnection()->insert(
            [
                'chat_id' => $model->getChatId(),
                'version_id' => $model->getVersionId(),
            ]
        )->into($this->tableName);
        $insertStatement->execute();
    }
}
