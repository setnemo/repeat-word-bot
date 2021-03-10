<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserNotification
 *
 * @ORM\Table(name="user_notification", uniqueConstraints={@ORM\UniqueConstraint(name="table_name_user_id_uindex", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="RepeatBot\Core\ORM\Repositories\UserNotificationRepository")
 *
 * @package RepeatBot\Core\ORM\Entities
 */
class UserNotification
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="silent", type="integer", nullable=true, options={"default"="1"})
     */
    private int $silent = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="deleted", type="integer", nullable=true)
     */
    private int $deleted = 0;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private Carbon $deletedAt;

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
    public function getDeleted(): int
    {
        return $this->deleted;
    }

    /**
     * @param int $deleted
     */
    public function setDeleted(int $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return Carbon
     */
    public function getDeletedAt(): Carbon
    {
        return $this->deletedAt;
    }

    /**
     * @param Carbon $deletedAt
     */
    public function setDeletedAt(Carbon $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
