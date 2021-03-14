<?php

declare(strict_types=1);

namespace Helper;

use Doctrine\ORM\EntityManager;
use RepeatBot\Common\Singleton;
use RepeatBot\Core\App;
use RepeatBot\Core\Database;

class ORM extends Singleton
{
    private static ?EntityManager $entityManager = null;

    final public static function getEntityManager(): EntityManager
    {
        if (null === self::$entityManager) {
            $config = App::getInstance()->init()->getConfig();
            self::$entityManager = Database::getInstance()->init($config)->getEntityManager();
        }

        return self::$entityManager;
    }
}
