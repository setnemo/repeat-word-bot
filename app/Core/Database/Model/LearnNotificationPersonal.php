<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Model;

use RepeatBot\Core\Database\BaseModel;

/**
 * Class LearnNotificationPersonal
 * @package RepeatBot\Core\Database\Model
 */
class LearnNotificationPersonal extends BaseModel
{
    /**
     * @var int
     */
    protected int $id;

    /**
     * @var int
     */
    protected int $user_id;

    /**
     * @var string
     */
    protected string $alarm;

    /**
     * @var string
     */
    protected string $message;

    /**
     * @var string
     */
    protected string $timezone;

    /**
     * @var string
     */
    protected string $created_at;

    /**
     * @var string
     */
    protected string $updated_at;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @return string
     */
    public function getAlarm(): string
    {
        return $this->alarm;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }
}
