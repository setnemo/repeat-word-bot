<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Model;

use RepeatBot\Core\Database\BaseModel;

/**
 * Class Export
 * @package RepeatBot\Core\Database\Model
 */
class Export extends BaseModel
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
     * @var int
     */
    protected int $chat_id;

    /**
     * @var string
     */
    protected string $word_type;
    
    /**
     * @var int
     */
    protected int $used;
    
    
    /**
     * @var string
     */
    protected string $created_at;

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
     * @return int
     */
    public function getChatId(): int
    {
        return $this->chat_id;
    }

    /**
     * @return string
     */
    public function getWordType(): string
    {
        return $this->word_type;
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
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }
}
