<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * VersionNotification
 *
 * @ORM\Table(name="version_notification", uniqueConstraints={@ORM\UniqueConstraint(name="version_notification_user_id_version_id_uindex", columns={"chat_id", "version_id"})})
 * @ORM\Entity(repositoryClass="RepeatBot\Core\ORM\Repositories\VersionNotificationRepository")
 *
 * @package RepeatBot\Core\ORM\Entities
 */
class VersionNotification
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
     * @ORM\Column(name="chat_id", type="bigint", nullable=true)
     */
    private int $chatId;

    /**
     * @var int
     *
     * @ORM\Column(name="version_id", type="integer", nullable=true)
     */
    private int $versionId;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private Carbon $createdAt;

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
     * @return int
     */
    public function getVersionId(): int
    {
        return $this->versionId;
    }

    /**
     * @param int $versionId
     */
    public function setVersionId(int $versionId): void
    {
        $this->versionId = $versionId;
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
}
