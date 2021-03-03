<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * VersionNotification
 *
 * @ORM\Table(name="version_notification", uniqueConstraints={@ORM\UniqueConstraint(name="version_notification_user_id_version_id_uindex", columns={"chat_id", "version_id"})})
 * @ORM\Entity
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
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="chat_id", type="bigint", nullable=true)
     */
    private $chatId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="version_id", type="integer", nullable=true)
     */
    private $versionId;

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';


}
