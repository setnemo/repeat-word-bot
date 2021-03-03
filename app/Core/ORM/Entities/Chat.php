<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chat
 *
 * @ORM\Table(name="chat", indexes={@ORM\Index(name="old_id", columns={"old_id"})})
 * @ORM\Entity
 */
class Chat
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"comment"="Unique identifier for this chat"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=0, nullable=false, options={"comment"="Type of chat, can be either private, group, supergroup or channel"})
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Title, for supergroups, channels and group chats"})
     */
    private $title = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Username, for private chats, supergroups and channels if available"})
     */
    private $username;

    /**
     * @var string|null
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="First name of the other party in a private chat"})
     */
    private $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Last name of the other party in a private chat"})
     */
    private $lastName;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="all_members_are_administrators", type="boolean", nullable=true, options={"comment"="True if a all members of this group are admins"})
     */
    private $allMembersAreAdministrators = '0';

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment"="Entry date creation"})
     */
    private $createdAt;

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, options={"comment"="Entry date update"})
     */
    private $updatedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="old_id", type="bigint", nullable=true, options={"comment"="Unique chat identifier, this is filled when a group is converted to a supergroup"})
     */
    private $oldId;


}
