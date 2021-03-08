<?php

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * RequestLimiter
 *
 * @ORM\Table(name="request_limiter")
 * @ORM\Entity
 */
class RequestLimiter
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true,"comment"="Unique identifier for this entry"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="chat_id", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Unique chat identifier"})
     */
    private $chatId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="inline_message_id", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Identifier of the sent inline message"})
     */
    private $inlineMessageId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="method", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Request method"})
     */
    private $method;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment"="Entry date creation"})
     */
    private $createdAt;
}
