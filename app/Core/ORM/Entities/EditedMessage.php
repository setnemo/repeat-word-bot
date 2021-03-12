<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * EditedMessage
 *
 * @ORM\Table(name="edited_message", indexes={@ORM\Index(name="message_id", columns={"message_id"}), @ORM\Index(name="chat_id_2", columns={"chat_id", "message_id"}), @ORM\Index(name="chat_id", columns={"chat_id"}), @ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 *
 * @package RepeatBot\Core\ORM\Entities
 */
class EditedMessage
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
     * @ORM\Column(name="chat_id", type="bigint", nullable=true, options={"comment"="Unique chat identifier"})
     */
    private $chatId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="message_id", type="bigint", nullable=true, options={"unsigned"=true,"comment"="Unique message identifier"})
     */
    private $messageId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=true, options={"comment"="Unique user identifier"})
     */
    private $userId;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="edit_date", type="datetime", nullable=true, options={"comment"="Date the message was edited in timestamp format"})
     */
    private $editDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="text", type="text", length=65535, nullable=true, options={"comment"="For text messages, the actual UTF-8 text of the message max message length 4096 char utf8"})
     */
    private $text;

    /**
     * @var string|null
     *
     * @ORM\Column(name="entities", type="text", length=65535, nullable=true, options={"comment"="For text messages, special entities like usernames, URLs, bot commands, etc. that appear in the text"})
     */
    private $entities;

    /**
     * @var string|null
     *
     * @ORM\Column(name="caption", type="text", length=65535, nullable=true, options={"comment"="For message with caption, the actual UTF-8 text of the caption"})
     */
    private $caption;
}
