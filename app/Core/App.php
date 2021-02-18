<?php

declare(strict_types=1);

namespace RepeatBot\Core;

use Dotenv\Dotenv;
use RepeatBot\Common\Config;
use RepeatBot\Common\Singleton;

/**
 * Class App
 * @package RepeatBot\Core
 */
final class App extends Singleton
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param string|null $path
     * @param Config|null $config
     *
     * @return App
     */
    public function init(string $path = null, Config $config = null): self
    {
        if (is_null($path)) {
            $paths = explode('/', __DIR__);
            array_pop($paths);
            array_pop($paths);
            $path = implode('/', $paths);
        }
        $env = Dotenv::createUnsafeImmutable($path . '/');
        $env->load();

        if (is_null($config)) {
            $config = new Config();
        }
        $this->config = $config;

        return $this;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}
