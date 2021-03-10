<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\ValueObjects;

/**
 * Class InactiveUser
 * @package RepeatBot\Core\ORM\ValueObjects
 */
class InactiveUser
{
    /**
     * @var int
     */
    protected int $userId;

    /**
     * @var int
     */
    protected int $silent;

    /**
     * @var string
     */
    protected string $message;

    /**
     * InactiveUser constructor.
     *
     * @param int    $userId
     * @param int    $silent
     * @param string $message
     */
    public function __construct(int $userId, int $silent, string $message)
    {
        $this->userId = $userId;
        $this->silent = $silent;
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
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
