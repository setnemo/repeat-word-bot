<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * Export
 *
 * @ORM\Table(name="export", indexes={@ORM\Index(name="export_user_id_index", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="RepeatBot\Core\ORM\Repositories\ExportRepository")
 *
 * @package RepeatBot\Core\ORM\Entities
 */
class Export
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
     * @var int
     *
     * @ORM\Column(name="chat_id", type="bigint", nullable=false)
     */
    private int $chatId;

    /**
     * @var string
     *
     * @ORM\Column(name="word_type", type="string", length=255, nullable=true)
     */
    private string $wordType;

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
     * @return string
     */
    public function getWordType(): string
    {
        return $this->wordType;
    }

    /**
     * @param string $wordType
     */
    public function setWordType(string $wordType): void
    {
        $this->wordType = $wordType;
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
    public function getChatId(): int
    {
        return $this->chatId;
    }

    /**
     * @param int $chatId
     */
    public function setChatId(int $chatId): void
    {
        $this->chatId = $chatId;
    }
}
