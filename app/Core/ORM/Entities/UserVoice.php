<?php

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserVoice
 *
 * @ORM\Table(name="user_voice", uniqueConstraints={@ORM\UniqueConstraint(name="user_voice_user_id_voice_uindex", columns={"user_id", "voice"})})
 * @ORM\Entity(repositoryClass="RepeatBot\Core\ORM\Repositories\UserVoiceRepository")
 */
class UserVoice
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
     * @ORM\Column(name="user_id", type="bigint", nullable=true)
     */
    private int $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="voice", type="string", length=255, nullable=false)
     */
    private string $voice;

    /**
     * @var int
     *
     * @ORM\Column(name="used", type="integer", nullable=false, options={"default"="0"})
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
     * @return string
     */
    public function getVoice(): string
    {
        return $this->voice;
    }

    /**
     * @param string $voice
     */
    public function setVoice(string $voice): void
    {
        $this->voice = $voice;
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
}
