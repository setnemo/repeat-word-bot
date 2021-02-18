<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database;

use FaaPz\PDO\Database as DB;

/**
 * Class BaseRepository
 * @package RepeatBot\Core\Database
 */
class BaseRepository
{
    /**
     * @var DB
     */
    protected DB $connection;

    /**
     * BaseRepository constructor.
     *
     * @param DB $database
     */
    public function __construct(DB $database)
    {
        $this->connection = $database;
    }

    /**
     * @return DB
     */
    public function getConnection(): DB
    {
        return $this->connection;
    }
}
