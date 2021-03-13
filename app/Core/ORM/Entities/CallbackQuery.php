<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * CallbackQuery
 *
 * @ORM\Table(name="callback_query", indexes={@ORM\Index(name="chat_id", columns={"chat_id"}), @ORM\Index(name="chat_id_2", columns={"chat_id", "message_id"}), @ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="message_id", columns={"message_id"})})
 * @ORM\Entity
 *
 * @package RepeatBot\Core\ORM\Entities
 */
class CallbackQuery
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
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true,"comment"="Unique identifier for this query"})
     */
    private int $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=true, options={"comment"="Unique user identifier"})
     */
    private ?int $userId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="chat_id", type="bigint", nullable=true, options={"comment"="Unique chat identifier"})
     */
    private ?int $chatId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="message_id", type="bigint", nullable=true, options={"unsigned"=true,"comment"="Unique message identifier"})
     */
    private ?int $messageId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="inline_message_id", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Identifier of the message sent via the bot in inline mode, that originated the query"})
     */
    private ?string $inlineMessageId;

    /**
     * @var string
     *
     * @ORM\Column(name="chat_instance", type="string", length=255, nullable=false, options={"fixed"=true,"comment"="Global identifier, uniquely corresponding to the chat to which the message with the callback button was sent"})
     */
    private string $chatInstance = '';

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="string", length=255, nullable=false, options={"fixed"=true,"comment"="Data associated with the callback button"})
     */
    private string $data = '';

    /**
     * @var string
     *
     * @ORM\Column(name="game_short_name", type="string", length=255, nullable=false, options={"fixed"=true,"comment"="Short name of a Game to be returned, serves as the unique identifier for the game"})
     */
    private string $gameShortName = '';

    /**
     * @var Carbon
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment"="Entry date creation"})
     */
    private Carbon $createdAt;
}
