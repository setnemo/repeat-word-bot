<?php

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * LearnNotificationPersonal
 *
 * @ORM\Table(name="learn_notification_personal", indexes={@ORM\Index(name="learn_notification_personal_user_id_index", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="RepeatBot\Core\ORM\Repositories\LearnNotificationPersonalRepository")
 */
class LearnNotificationPersonal
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
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
     * @var Carbon
     *
     * @ORM\Column(name="alarm", type="carbon_time", nullable=false)
     */
    private Carbon $alarm;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=false)
     */
    private string $message;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=255, nullable=false)
     */
    private string $timezone;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="created_at", type="carbon", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private Carbon $createdAt;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="updated_at", type="carbon", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
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
     * @return Carbon
     */
    public function getAlarm(): Carbon
    {
        return $this->alarm;
    }
    
    /**
     * @param Carbon $alarm
     */
    public function setAlarm(Carbon $alarm): void
    {
        $this->alarm = $alarm;
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
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }
    
    /**
     * @param string $timezone
     */
    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
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
