<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Model;

use RepeatBot\Core\Database\BaseModel;

/**
 * Class Rating
 * @package RepeatBot\Core\Database\Model
 */
class Rating extends BaseModel
{
    /**
     * @var int
     */
    protected int $id;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $created_at;

    /**
     * @var int
     */
    protected int $public;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * @return int
     */
    public function getPublic(): int
    {
        return $this->public;
    }
}
