<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserNotification
 *
 * @ORM\Table(name="user_notification", uniqueConstraints={@ORM\UniqueConstraint(name="table_name_user_id_uindex", columns={"user_id"})})
 * @ORM\Entity
 */
class UserNotification
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="silent", type="integer", nullable=true, options={"default"="1"})
     */
    private $silent = 1;

    /**
     * @var int|null
     *
     * @ORM\Column(name="deleted", type="integer", nullable=true)
     */
    private $deleted = '0';

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;


}
