<?php

declare(strict_types=1);

namespace RepeatBot\Core;

use Carbon\Doctrine\CarbonType;
use Carbon\Doctrine\DateTimeImmutableType;
use Carbon\Doctrine\DateTimeType;
use Doctrine\Common\Cache\PredisCache;
use Doctrine\DBAL\Types\Type;
use RepeatBot\Common\Config;
use RepeatBot\Common\Singleton;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use RepeatBot\Core\Cache as CoreCache;

/**
 * Class Database
 * @package RepeatBot\Core\Database
 */
class Database extends Singleton
{
    public const DEFAULT_TZ = 'Europe/Kiev';
    /**
     * @var EntityManager
     */
    protected EntityManager $entityManager;

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
        $isDevMode = (int)$config->getKey('database.dev_mode') === 1;
        $dbParams = array(
            'driver'   => 'pdo_mysql',
            'host'     => $host,
            'user'     => $user,
            'password' => $password,
            'dbname'   => $name,
            'charset'  => 'UTF8',
        );
        $paths = [$config->getKey('database.entity_path')];
        $redis = CoreCache::getInstance()->init($config)->getRedisSecond();
        $redis = new PredisCache($redis);
        $metadataConfiguration = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, $redis, false);
        $this->entityManager = EntityManager::create($dbParams, $metadataConfiguration);
        $conn = $this->entityManager->getConnection();
        Type::overrideType('time', DateTimeType::class);
        Type::overrideType('datetime', CarbonType::class);
        Type::overrideType('datetime_immutable', DateTimeImmutableType::class);
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
