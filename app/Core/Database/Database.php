<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database;

use Doctrine\Common\Cache\PredisCache;
use Doctrine\DBAL\Types\Type;
use RepeatBot\Common\Config;
use RepeatBot\Common\Singleton;
use FaaPz\PDO\Database as DB;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use RepeatBot\Core\Cache as CoreCache;

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
     * @var EntityManager
     */
    protected EntityManager $entityManager;

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
    
        $isDevMode = (int)$config->getKey('database.dev_mode') === 1;
        $dbParams = array(
            'driver'   => 'pdo_mysql',
            'host'     => $host,
            'user'     => $user,
            'password' => $password,
            'dbname'   => $name,
        );
        $paths = [$config->getKey('database.entity_path')];
        $redis = CoreCache::getInstance()->init($config)->getRedis();
        $redis = new PredisCache($redis);
        $config2 = Setup::createAnnotationMetadataConfiguration($paths, false, null, $redis, false);
        

        $this->entityManager = EntityManager::create($dbParams, $config2);
        $conn = $this->entityManager->getConnection();
    
        Type::addType('carbon_immutable', \Carbon\Doctrine\DateTimeImmutableType::class);
        Type::addType('carbon', \Carbon\Doctrine\CarbonType::class);
        Type::addType('carbon_time', \Carbon\Doctrine\DateTimeType::class);
//        Type::overrideType('carbon_immutable', DateTimeImmutableType::class);
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping('time', 'carbon_time');
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping('datetime', 'carbon');
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping('datetime_immutable', 'carbon_immutable');
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        return $this;
    }
    
    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }
}
