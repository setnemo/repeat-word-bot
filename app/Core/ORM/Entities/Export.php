<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Export
 *
 * @ORM\Table(name="export", indexes={@ORM\Index(name="export_user_id_index", columns={"user_id"})})
 * @ORM\Entity
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
     * @ORM\Column(name="chat_id", type="bigint", nullable=false)
     */
    private $chatId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="word_type", type="string", length=255, nullable=true)
     */
    private $wordType;

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
