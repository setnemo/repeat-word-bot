<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * LearnNotification
 *
 * @ORM\Table(name="learn_notification", indexes={@ORM\Index(name="event_created_index", columns={"created_at"})})
 * @ORM\Entity
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
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=false)
     */
    private $message;

    /**
     * @var int|null
     *
     * @ORM\Column(name="silent", type="integer", nullable=true)
     */
    private $silent = '0';

    /**
     * @var int|null
     *
     * @ORM\Column(name="used", type="integer", nullable=true)
     */
    private $used = '0';

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $updatedAt = 'CURRENT_TIMESTAMP';


}
