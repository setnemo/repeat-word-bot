<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * LearnNotification
 *
 * @ORM\Table(name="learn_notification", indexes={@ORM\Index(name="event_created_index", columns={"created_at"})})
 * @ORM\Entity(repositoryClass="RepeatBot\Core\ORM\Repositories\LearnNotificationRepository")
 *
 * @package RepeatBot\Core\ORM\Entities
 */
class LearnNotification
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     */
    private int $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=false)
     */
    private string $message;

    /**
     * @var int
     *
     * @ORM\Column(name="silent", type="integer", nullable=true)
     */
    private int $silent = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="used", type="integer", nullable=true)
     */
    private int $used = 0;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private Carbon $createdAt;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private Carbon $updatedAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getSilent(): int
    {
        return $this->silent;
    }

    /**
     * @param int $silent
     */
    public function setSilent(int $silent): void
    {
        $this->silent = $silent;
    }

    /**
     * @return int
     */
    public function getUsed(): int
    {
        return $this->used;
    }

    /**
     * @param int $used
     */
    public function setUsed(int $used): void
    {
        $this->used = $used;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * @param Carbon $createdAt
     */
    public function setCreatedAt(Carbon $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    /**
     * @param Carbon $updatedAt
     */
    public function setUpdatedAt(Carbon $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
