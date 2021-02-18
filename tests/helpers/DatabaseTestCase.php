<?php

declare(strict_types=1);

namespace Tests\Helpers;

use PHPUnit\Framework\TestCase;
use FaaPz\PDO\Database as DB;
use RepeatBot\Core\App;

abstract class DatabaseTestCase extends TestCase
{
    /**
     * @var DB|null
     */
    private ?DB $conn = null;

    /**
     * @return DB
     */
    final public function getConnection(): DB
    {
        if ($this->conn === null) {
            $config = App::getInstance()->init()->getConfig();
            $host = $config->getKey('database.host');
            $name = $config->getKey('database.name');
            $user = $config->getKey('database.user');
            $password = $config->getKey('database.password');
            $this->conn = new DB("mysql:host={$host};dbname={$name};charset=utf8", $user, $password);
        }

        return $this->conn;
    }
}
