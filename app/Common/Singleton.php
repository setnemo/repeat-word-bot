<?php

declare(strict_types=1);

namespace RepeatBot\Common;

/**
 * Class Singleton
 *
 * @package RepeatBot\Common
 */
class Singleton
{
    protected static array $instances = [];

    /**
     * Singleton constructor.
     */
    protected function __construct()
    {
        // do nothing
    }

    /**
     * Disable clone object.
     */
    protected function __clone()
    {
        // do nothing
    }

    /**
     * Disable serialize object.
     */
    public function __sleep()
    {
        // do nothing
    }

    /**
     * Disable deserialize object.
     */
    public function __wakeup()
    {
        // do nothing
    }

    /**
     * @return static
     */
    public static function getInstance(): Singleton
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }
}
