<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Model;

use RepeatBot\Core\Database\BaseModel;

/**
 * Class TrainingSave
 * @package RepeatBot\Core\Database\Model
 */
class TrainingSave extends BaseModel
{
    /**
     * @var int
     */
    protected int $id;

    /**
     * @var int
     */
    protected int $user_id;

    /**
     * @var string
     */
    protected string $word;

    /**
     * @var string
     */
    protected string $status;

    /**
     * @var string
     */
    protected string $type;

    /**
     * @var string
     */
    protected string $repeat;

    /**
     * @var int
     */
    protected int $used;

    /**
     * @var string
     */
    protected string $updated_at;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getWord(): string
    {
        return $this->word;
    }

    /**
     * @return string
     */
    public function getRepeat(): string
    {
        return $this->repeat;
    }

    /**
     * @return int
     */
    public function getUsed(): int
    {
        return $this->used;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }
}
