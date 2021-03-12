<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * ChosenInlineResult
 *
 * @ORM\Table(name="chosen_inline_result", indexes={@ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 *
 * @package RepeatBot\Core\ORM\Entities
 */
class ChosenInlineResult
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
     * @var string
     *
     * @ORM\Column(name="result_id", type="string", length=255, nullable=false, options={"fixed"=true,"comment"="The unique identifier for the result that was chosen"})
     */
    private $resultId = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=true, options={"comment"="The user that chose the result"})
     */
    private $userId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Sender location, only for bots that require user location"})
     */
    private $location;

    /**
     * @var string|null
     *
     * @ORM\Column(name="inline_message_id", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Identifier of the sent inline message"})
     */
    private $inlineMessageId;

    /**
     * @var string
     *
     * @ORM\Column(name="query", type="text", length=65535, nullable=false, options={"comment"="The query that was used to obtain the result"})
     */
    private $query;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment"="Entry date creation"})
     */
    private $createdAt;
}
