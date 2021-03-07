<?php

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * Training
 *
 * @ORM\Table(name="training", uniqueConstraints={@ORM\UniqueConstraint(name="training_word_type_collection_id_uindex", columns={"word", "type", "collection_id", "user_id"})}, indexes={@ORM\Index(name="repeat", columns={"repeat"}), @ORM\Index(name="training_word_id_index", columns={"word_id"}), @ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="training_type_index", columns={"type"})})
 * @ORM\Entity(repositoryClass="RepeatBot\Core\ORM\Repositories\TrainingRepository")
 */
class Training
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @var int
     *
     * @ORM\Column(name="word_id", type="integer", nullable=false)
     */
    private int $wordId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     */
    private int $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="collection_id", type="integer", nullable=false)
     */
    private int $collectionId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private string $type;

    /**
     * @var string
     *
     * @ORM\Column(name="word", type="string", length=255, nullable=false)
     */
    private string $word;

    /**
     * @var string
     *
     * @ORM\Column(name="translate", type="text", length=0, nullable=true)
     */
    private string $translate;

    /**
     * @var string
     *
     * @ORM\Column(name="voice", type="text", length=65535, nullable=false)
     */
    private string $voice;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=0, nullable=true, options={"default"="first"})
     */
    private string $status = 'first';

    /**
     * @var Carbon
     *
     * @ORM\Column(name="repeat", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private Carbon $repeat;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private Carbon $createdAt;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private Carbon $updatedAt;
    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
    /**
     * @return int
     */
    public function getWordId(): int
    {
        return $this->wordId;
    }
    
    /**
     * @param int $wordId
     */
    public function setWordId(int $wordId): void
    {
        $this->wordId = $wordId;
    }
    
    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
    
    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
    
    /**
     * @return int
     */
    public function getCollectionId(): int
    {
        return $this->collectionId;
    }
    
    /**
     * @param int $collectionId
     */
    public function setCollectionId(int $collectionId): void
    {
        $this->collectionId = $collectionId;
    }
    
    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
    
    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
    
    /**
     * @return string
     */
    public function getWord(): string
    {
        return $this->word;
    }
    
    /**
     * @param string $word
     */
    public function setWord(string $word): void
    {
        $this->word = $word;
    }
    
    /**
     * @return string
     */
    public function getTranslate(): string
    {
        return $this->translate;
    }
    
    /**
     * @param string $translate
     */
    public function setTranslate(string $translate): void
    {
        $this->translate = $translate;
    }
    
    /**
     * @return string
     */
    public function getVoice(): string
    {
        return $this->voice;
    }
    
    /**
     * @param string $voice
     */
    public function setVoice(string $voice): void
    {
        $this->voice = $voice;
    }
    
    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
    
    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
    
    /**
     * @return Carbon
     */
    public function getRepeat(): Carbon
    {
        return $this->repeat;
    }
    
    /**
     * @param Carbon $repeat
     */
    public function setRepeat(Carbon $repeat): void
    {
        $this->repeat = $repeat;
    }
    
    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }
    
    /**
     * @param Carbon $createdAt
     */
    public function setCreatedAt(Carbon $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    
    /**
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }
    
    /**
     * @param Carbon $updatedAt
     */
    public function setUpdatedAt(Carbon $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
    
    
}
