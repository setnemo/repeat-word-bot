<?php
declare(strict_types = 1);

namespace RepeatBot\Core\ORM\ValueObjects;

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
