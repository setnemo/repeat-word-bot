<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * Chat
 *
 * @ORM\Table(name="chat", indexes={@ORM\Index(name="old_id", columns={"old_id"})})
 * @ORM\Entity
 *
 * @package RepeatBot\Core\ORM\Entities
 */
class Chat
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
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"comment"="Unique identifier for this chat"})
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=0, nullable=false, options={"comment"="Type of chat, can be either private, group, supergroup or channel"})
     */
    private string $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Title, for supergroups, channels and group chats"})
     */
    private ?string $title = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Username, for private chats, supergroups and channels if available"})
     */
    private ?string $username;

    /**
     * @var string|null
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="First name of the other party in a private chat"})
     */
    private ?string $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Last name of the other party in a private chat"})
     */
    private ?string $lastName;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="all_members_are_administrators", type="boolean", nullable=true, options={"comment"="True if a all members of this group are admins"})
     */
    private ?bool $allMembersAreAdministrators = false;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment"="Entry date creation"})
     */
    private Carbon $createdAt;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, options={"comment"="Entry date update"})
     */
    private Carbon $updatedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="old_id", type="bigint", nullable=true, options={"comment"="Unique chat identifier, this is filled when a group is converted to a supergroup"})
     */
    private ?int $oldId;
}
