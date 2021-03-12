<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * PollAnswer
 *
 * @ORM\Table(name="poll_answer")
 * @ORM\Entity
 *
 * @package RepeatBot\Core\ORM\Entities
 */
class PollAnswer
{
    /**
     * @var int
     *
     * @ORM\Column(name="poll_id", type="bigint", nullable=false, options={"unsigned"=true,"comment"="Unique poll identifier"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $pollId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false, options={"comment"="The user, who changed the answer to the poll"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="option_ids", type="text", length=65535, nullable=false, options={"comment"="0-based identifiers of answer options, chosen by the user. May be empty if the user retracted their vote."})
     */
    private $optionIds;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment"="Entry date creation"})
     */
    private $createdAt;
}
