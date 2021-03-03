<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user", indexes={@ORM\Index(name="username", columns={"username"})})
 * @ORM\Entity
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"comment"="Unique identifier for this user or bot"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_bot", type="boolean", nullable=true, options={"comment"="True, if this user is a bot"})
     */
    private $isBot = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=false, options={"fixed"=true,"comment"="User's or bot's first name"})
     */
    private $firstName = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="User's or bot's last name"})
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="string", length=191, nullable=true, options={"fixed"=true,"comment"="User's or bot's username"})
     */
    private $username;

    /**
     * @var string|null
     *
     * @ORM\Column(name="language_code", type="string", length=10, nullable=true, options={"fixed"=true,"comment"="IETF language tag of the user's language"})
     */
    private $languageCode;

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


}
