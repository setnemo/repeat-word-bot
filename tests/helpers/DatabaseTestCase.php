<?php

declare(strict_types=1);

namespace Tests\Helpers;

use Carbon\Doctrine\CarbonType;
use Carbon\Doctrine\DateTimeImmutableType;
use Carbon\Doctrine\DateTimeType;
use Doctrine\Common\Cache\PredisCache;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache as CoreCache;

abstract class DatabaseTestCase extends TestCase
{
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;
    
    final public function getConnection(): EntityManager
    {
        if (null === $this->entityManager) {
            $config = App::getInstance()->init()->getConfig();
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
            $redis = CoreCache::getInstance()->init($config)->getRedis();
            $redis = new PredisCache($redis);
            $metadataConfiguration = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, $redis, false);
            $this->entityManager = EntityManager::create($dbParams, $metadataConfiguration);
            $conn = $this->entityManager->getConnection();
            Type::overrideType('time', DateTimeType::class);
            Type::overrideType('datetime', CarbonType::class);
            Type::overrideType('datetime_immutable', DateTimeImmutableType::class);
            $conn->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        }

        return $this->entityManager;
    }
}
