<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Repository;

use FaaPz\PDO\Clause\Conditional;
use FaaPz\PDO\Clause\Limit;
use RepeatBot\Core\Database\BaseRepository;
use RepeatBot\Core\Database\Model\Version;

/**
 * Class VersionRepository
 * @package RepeatBot\Core\Database\Repository
 */
class VersionRepository extends BaseRepository
{
    protected string $tableName = 'version';

    /**
     * @param array $params
     *
     * @return Version
     */
    public function getNewModel(array $params): Version
    {
        return new Version($params);
    }

    /**
     * @return Version
     */
    public function getNewLatestVersion(): Version
    {
        $selectStatement = $this->
        getConnection()->select(['*'])
            ->from($this->tableName)
            ->where(
                new Conditional('used', '=', 0)
            )
            ->orderBy('created_at', 'desc')
            ->limit(new Limit(1));
        $stmt = $selectStatement->execute();
        $result = $stmt->fetchAll()[0] ?? [];
        return $this->getNewModel($result);
    }

    /**
     * @param Version $version
     */
    public function applyVersion(Version $version): void
    {
        $updateStatement = $this->getConnection()->update(['used' => 1])
            ->table($this->tableName)
            ->where(new Conditional('id', '=', $version->getId()));
        $affectedRows = $updateStatement->execute();
    }
}
