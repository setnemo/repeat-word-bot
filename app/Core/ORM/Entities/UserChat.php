<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserChat
 *
 * @ORM\Table(name="user_chat", indexes={@ORM\Index(name="chat_id", columns={"chat_id"})})
 * @ORM\Entity
 */
class UserChat
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false, options={"comment"="Unique user identifier"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="chat_id", type="bigint", nullable=false, options={"comment"="Unique user or chat identifier"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $chatId;


}
