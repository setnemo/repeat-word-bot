<?php

declare(strict_types=1);

namespace RepeatBot\Core;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use RepeatBot\Common\Config;
use RepeatBot\Common\Singleton;

/**
 * Class Log
 * @package RepeatBot\Core
 */
final class Log extends Singleton
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Config               $config
     * @param LoggerInterface|null $logger
     *
     * @return Log
     */
    public function init(Config $config, LoggerInterface $logger = null): self
    {
        if ($logger) {
            $this->logger = $logger;
        } else {
            $logPath = $config->getKey('logger.path');
            $logName = $config->getKey('logger.name');
            $logFileName = $config->getKey('logger.filename');
            $logLevel = intval($config->getKey('logger.level'));
            $this->logger = new Logger($logName);
            $this->logger->pushHandler(new StreamHandler($logPath . $logFileName, $logLevel));
        }

        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
