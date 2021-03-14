<?php

declare(strict_types=1);

namespace Helper;

use RepeatBot\Core\App;
use RepeatBot\Core\Cache as CoreCache;

class Cache
{
    private static ?CoreCache $cache = null;

    final public static function getCache(): CoreCache
    {
        if (null === self::$cache) {
            $config = App::getInstance()->init()->getConfig();
            self::$cache = CoreCache::getInstance()->init($config);
        }

        return self::$cache;
    }
}
