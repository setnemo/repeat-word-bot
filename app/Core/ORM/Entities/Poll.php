<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * Poll
 *
 * @ORM\Table(name="poll")
 * @ORM\Entity
 *
 * @package RepeatBot\Core\ORM\Entities
 */
class Poll
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
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true,"comment"="Unique poll identifier"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="text", length=65535, nullable=false, options={"comment"="Poll question"})
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="options", type="text", length=65535, nullable=false, options={"comment"="List of poll options"})
     */
    private $options;

    /**
     * @var int|null
     *
     * @ORM\Column(name="total_voter_count", type="integer", nullable=true, options={"unsigned"=true,"comment"="Total number of users that voted in the poll"})
     */
    private $totalVoterCount;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_closed", type="boolean", nullable=true, options={"comment"="True, if the poll is closed"})
     */
    private $isClosed = false;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_anonymous", type="boolean", nullable=true, options={"default"="1","comment"="True, if the poll is anonymous"})
     */
    private $isAnonymous = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Poll type, currently can be “regular” or “quiz”"})
     */
    private $type;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="allows_multiple_answers", type="boolean", nullable=true, options={"comment"="True, if the poll allows multiple answers"})
     */
    private $allowsMultipleAnswers = false;

    /**
     * @var int|null
     *
     * @ORM\Column(name="correct_option_id", type="integer", nullable=true, options={"unsigned"=true,"comment"="0-based identifier of the correct answer option. Available only for polls in the quiz mode, which are closed, or was sent (not forwarded) by the bot or to the private chat with the bot."})
     */
    private $correctOptionId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="explanation", type="string", length=255, nullable=true, options={"comment"="Text that is shown when a user chooses an incorrect answer or taps on the lamp icon in a quiz-style poll, 0-200 characters"})
     */
    private $explanation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="explanation_entities", type="text", length=65535, nullable=true, options={"comment"="Special entities like usernames, URLs, bot commands, etc. that appear in the explanation"})
     */
    private $explanationEntities;

    /**
     * @var int|null
     *
     * @ORM\Column(name="open_period", type="integer", nullable=true, options={"unsigned"=true,"comment"="Amount of time in seconds the poll will be active after creation"})
     */
    private $openPeriod;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="close_date", type="datetime", nullable=true, options={"comment"="Point in time (Unix timestamp) when the poll will be automatically closed"})
     */
    private $closeDate;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment"="Entry date creation"})
     */
    private $createdAt;
}
