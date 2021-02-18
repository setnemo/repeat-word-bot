<?php

declare(strict_types=1);

namespace RepeatBot\Common;

use Adbar\Dot;

/**
 * Class Config
 * @package RepeatBot\Common
 */
class Config
{
    /**
     * @var Dot
     */
    private Dot $config;

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $config = include_once __DIR__ . '/../../config/app.php';
        }

        $this->config = new Dot($config);
    }

    public function getKey(string $key): string
    {
        return $this->config->get($key);
    }
}
