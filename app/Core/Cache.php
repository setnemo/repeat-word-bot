<?php

declare(strict_types=1);

namespace RepeatBot\Core;

use JetBrains\PhpStorm\Pure;
use Predis\Client;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Common\Config;
use RepeatBot\Common\Singleton;

/**
 * Class Redis
 * @package RepeatBot\Core
 */
class Cache extends Singleton
{
    public const PREFIX = 'repeat_bot_';
    /**
     * @var Client
     */
    private Client $redisFirst;
    /**
     * @var Client
     */
    private Client $redisSecond;

    /**
     * @param Config $config
     *
     * @return $this
     */
    public function init(Config $config): self
    {
        $this->redisFirst = new Client([
            'host' => $config->getKey('redis.host'),
            'port' => intval($config->getKey('redis.port')),
            'database' => $config->getKey('redis.database'),
        ]);
        $this->redisSecond = new Client([
            'host' => $config->getKey('redis.host'),
            'port' => intval($config->getKey('redis.port')),
            'database' => $config->getKey('redis.database2'),
        ]);
        return $this;
    }

    /**
     * @return Client
     */
    public function getRedis(): Client
    {
        return $this->redisFirst;
    }

    /**
     * @param string $source
     *
     * @return string
     */
    public function getCacheSlug(string $source): string
    {
        return self::PREFIX . $source;
    }

    /**
     * @param int    $userId
     * @param string $type
     * @param int    $value
     */
    public function setTrainingStatusId(int $userId, string $type, int $value): void
    {
        $redis = $this->getRedis();
        $slug = $this->getSlugTraining($userId, $type);
        $redis->set($slug, $value);
    }

    /**
     * @param int    $userId
     * @param string $type
     */
    public function setTrainingStatus(int $userId, string $type): void
    {
        $redis = $this->getRedis();
        $slug = $this->getCacheSlugTrainingStatus($userId, $type);
        $redis->set($slug, 1);
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return int
     */
    public function skipTrainings(int $userId, string $type): void
    {
        $redis = $this->getRedis();
        $slug = $this->getCacheSlugSkip($userId, $type);
        $redis->set($slug, 1, 'EX', 3600);
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return int
     */
    public function saveOneYear(int $userId, string $type): void
    {
        $redis = $this->getRedis();
        $slug = $this->getCacheOneYear($userId, $type);
        $redis->set($slug, 1, 'EX', 3600);
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return int
     */
    public function checkOneYear(int $userId, string $type): int
    {
        return $this->getRedis()->exists($this->getCacheOneYear($userId, $type));
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return int
     */
    public function checkSkipTrainings(int $userId, string $type): int
    {
        return $this->getRedis()->exists($this->getCacheSlugSkip($userId, $type));
    }

    /**
     * @param int    $userId
     * @param string $type
     */
    public function removeSkipTrainings(int $userId, string $type): void
    {
        $this->getRedis()->del($this->getCacheSlugSkip($userId, $type));
    }

    /**
     * @param int    $userId
     * @param string $type
     */
    public function removeOneYear(int $userId, string $type): void
    {
        $this->getRedis()->del($this->getCacheOneYear($userId, $type));
    }

    /**
     * @param int $userId
     *
     * @return ?string
     */
    public function checkTrainingsStatus(int $userId): ?string
    {
        $types = BotHelper::getTrainingTypes();
        $redis = $this->getRedis();

        foreach ($types as $type) {
            if ($redis->exists($this->getCacheSlugTrainingStatus($userId, $type))) {
                return $type;
            }
        }

        return null;
    }


    /**
     * @param int    $userId
     * @param string $type
     */
    public function removeTrainingsStatus(int $userId, string $type): void
    {
        $this->getRedis()->del($this->getCacheSlugTrainingStatus($userId, $type));
    }


    /**
     * @param int $userId
     *
     * @return ?string
     */
    public function checkTrainings(int $userId): ?string
    {
        $types = BotHelper::getTrainingTypes();
        $redis = $this->getRedis();

        foreach ($types as $type) {
            if ($redis->exists($this->getSlugTraining($userId, $type))) {
                return $type;
            }
        }

        return null;
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return int
     */
    public function getTrainings(int $userId, string $type): int
    {
        return intval($this->getRedis()->get($this->getSlugTraining($userId, $type)));
    }

    /**
     * @param int    $userId
     * @param string $type
     */
    public function removeTrainings(int $userId, string $type): void
    {
        $this->getRedis()->del($this->getSlugTraining($userId, $type));
    }

    /**
     * @param int $userId
     *
     * @return int
     */
    public function getPriority(int $userId): int
    {
        $slug = $this->getSlugPriority($userId);

        return intval($this->getRedis()->get($slug));
    }

    /**
     * @param int $userId
     * @param int $priority
     */
    public function setPriority(int $userId, int $priority): void
    {
        $slug = $this->getSlugPriority($userId);

        $this->getRedis()->set($slug, $priority);
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return string
     */
    #[Pure] private function getCacheSlugTrainingStatus(int $userId, string $type): string
    {
        return $this->getCacheSlug("{$userId}_{$type}_status");
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return string
     */
    #[Pure] private function getCacheOneYear(int $userId, string $type): string
    {
        return $this->getCacheSlug("{$userId}_{$type}_one_year");
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return string
     */
    #[Pure] private function getCacheSlugSkip(int $userId, string $type): string
    {
        return $this->getCacheSlug("{$userId}_{$type}_skip");
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return string
     */
    #[Pure] private function getSlugTraining(int $userId, string $type): string
    {
        return $this->getCacheSlug("{$userId}_{$type}");
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return string
     */
    #[Pure] private function getSlugPriority(int $userId): string
    {
        return $this->getCacheSlug("{$userId}_priority");
    }
}
