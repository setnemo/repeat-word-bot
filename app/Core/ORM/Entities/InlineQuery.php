<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * InlineQuery
 *
 * @ORM\Table(name="inline_query", indexes={@ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 *
 * @package RepeatBot\Core\ORM\Entities
 */
class InlineQuery
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true,"comment"="Unique identifier for this query"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=true, options={"comment"="Unique user identifier"})
     */
    private $userId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Location of the user"})
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="query", type="text", length=65535, nullable=false, options={"comment"="Text of the query"})
     */
    private $query;

    /**
     * @var string|null
     *
     * @ORM\Column(name="offset", type="string", length=255, nullable=true, options={"fixed"=true,"comment"="Offset of the result"})
     */
    private $offset;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment"="Entry date creation"})
     */
    private $createdAt;
}
