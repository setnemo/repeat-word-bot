<?php

declare(strict_types=1);

namespace RepeatBot\Core;

use Prometheus\CollectorRegistry;
use Prometheus\Exception\MetricsRegistrationException;
use Prometheus\Storage\Redis;
use RepeatBot\Common\Config;
use RepeatBot\Common\Singleton;

/**
 * Class Metric
 * @package RepeatBot\Core
 */
final class Metric extends Singleton
{
    const NAMESPACE = 'repeat_bot';
    /**
     * @var CollectorRegistry
     */
    private CollectorRegistry $registry;

    /**
     * @param Config               $config
     *
     * @return Metric
     */
    public function init(Config $config): self
    {
        Redis::setDefaultOptions(
            [
                'host' => $config->getKey('redis.host'),
                'port' => intval($config->getKey('redis.port')),
                'database' => intval($config->getKey('redis.database')),
                'password' => null,
                'timeout' => 0.1, // in seconds
                'read_timeout' => '10', // in seconds
                'persistent_connections' => false
            ]
        );
        $this->registry = CollectorRegistry::getDefault();

        return $this;
    }

    /**
     * @return CollectorRegistry
     */
    public function getRegistry(): CollectorRegistry
    {
        return $this->registry;
    }

    /**
     * @param string $metricName
     *
     * @throws MetricsRegistrationException
     */
    public function increaseMetric(string $metricName): void
    {
        $counter = $this->registry->getOrRegisterCounter(self::NAMESPACE, $metricName, 'it increases');
        $counter->incBy(1, []);
    }
}
