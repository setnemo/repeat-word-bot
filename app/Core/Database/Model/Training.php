<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Model;

use RepeatBot\Core\Database\BaseModel;

/**
 * Class Training
 * @package RepeatBot\Core\Database\Model
 */
class Training extends BaseModel
{
    /**
     * @var int
     */
    protected int $id;

    /**
     * @var int
     */
    protected int $word_id;

    /**
     * @var int
     */
    protected int $user_id;

    /**
     * @var int
     */
    protected int $collection_id;

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
    protected string $word;

    /**
     * @var string
     */
    protected string $translate;

    /**
     * @var string
     */
    protected string $voice;

    /**
     * @var string
     */
    protected string $repeat;

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
    public function getWordId(): int
    {
        return $this->word_id;
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
    public function getCollectionId(): int
    {
        return $this->collection_id;
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
    public function getTranslate(): string
    {
        return $this->translate;
    }

    /**
     * @return string
     */
    public function getVoice(): string
    {
        return $this->voice;
    }

    /**
     * @return string
     */
    public function getRepeat(): string
    {
        return $this->repeat;
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
