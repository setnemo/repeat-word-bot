<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Model;

use RepeatBot\Core\Database\BaseModel;

/**
 * Class UserNotification
 * @package RepeatBot\Core\Database\Model
 */
class UserNotification extends BaseModel
{
    /**
     * @var int
     */
    protected int $user_id;

    /**
     * @var int
     */
    protected int $silent;

    /**
     * @var int
     */
    protected int $deleted;

    /**
     * @var ?string
     */
    protected ?string $deleted_at;

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @return int
     */
    public function getSilent(): int
    {
        return $this->silent;
    }

    /**
     * @return int
     */
    public function getDeleted(): int
    {
        return $this->deleted;
    }

    /**
     * @return ?string
     */
    public function getDeletedAt(): ?string
    {
        return $this->deleted_at;
    }
}
