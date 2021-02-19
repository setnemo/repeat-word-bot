<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Model;

use RepeatBot\Core\Database\BaseModel;

/**
 * Class Word
 * @package RepeatBot\Core\Database\Model
 */
class Word extends BaseModel
{
    /**
     * @var int
     */
    protected int $id;

    /**
     * @var string
     */
    protected string $word;

    /**
     * @var string
     */
    protected string $voice;

    /**
     * @var int
     */
    protected int $collection_id;

    /**
     * @var string
     */
    protected string $translate;

    /**
     * @var string
     */
    protected string $created_at;

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
    public function getWord(): string
    {
        return $this->word;
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
    public function getTranslate(): string
    {
        return $this->translate;
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
    public function getVoice(): string
    {
        return $this->voice;
    }
}
