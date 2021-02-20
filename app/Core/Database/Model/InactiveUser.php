<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Model;

use RepeatBot\Core\Database\BaseModel;

/**
 * Class InactiveUser
 * @package RepeatBot\Core\Database\Model
 */
class InactiveUser extends BaseModel
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
     * @var string
     */
    protected string $message;

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
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
