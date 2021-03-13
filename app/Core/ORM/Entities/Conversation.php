<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * Conversation
 *
 * @ORM\Table(name="conversation", indexes={@ORM\Index(name="chat_id", columns={"chat_id"}), @ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="status", columns={"status"})})
 * @ORM\Entity
 *
 * @package RepeatBot\Core\ORM\Entities
 */
class Conversation
{
    /**
     * @var int
     *
     * @ORM\Column(name="primary_id", type="bigint", nullable=false, options={"unsigned"=true,"comment"="Unique
 * identifier for this query"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $primaryId;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true,"comment"="Unique identifier for this entry"})
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=true, options={"comment"="Unique user identifier"})
     */
    private $userId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="chat_id", type="bigint", nullable=true, options={"comment"="Unique user or chat identifier"})
     */
    private $chatId;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=0, nullable=false, options={"default"="active","comment"="Conversation state"})
     */
    private $status = 'active';

    /**
     * @var string|null
     *
     * @ORM\Column(name="command", type="string", length=160, nullable=true, options={"comment"="Default command to execute"})
     */
    private $command = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", length=65535, nullable=true, options={"comment"="Data stored from command"})
     */
    private $notes;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment"="Entry date creation"})
     */
    private $createdAt;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, options={"comment"="Entry date update"})
     */
    private $updatedAt;
}
