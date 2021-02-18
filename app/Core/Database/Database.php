<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database;

use RepeatBot\Common\Config;
use RepeatBot\Common\Singleton;
use FaaPz\PDO\Database as DB;

/**
 * Class Database
 * @package RepeatBot\Core\Database
 */
class Database extends Singleton
{
    /**
     * @var DB
     */
    protected DB $connection;

    /**
     * @return DB
     */
    public function getConnection(): DB
    {
        return $this->connection;
    }

    /**
     * @param Config $config
     *
     * @return Database
     */
    public function init(Config $config): self
    {
        $host = $config->getKey('database.host');
        $name = $config->getKey('database.name');
        $user = $config->getKey('database.user');
        $password = $config->getKey('database.password');
        $this->connection = new DB("mysql:host={$host};dbname={$name};charset=utf8", $user, $password);
        return $this;
    }
}
