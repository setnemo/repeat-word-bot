<?php

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * Training
 *
 * @ORM\Table(name="training", uniqueConstraints={@ORM\UniqueConstraint(name="training_word_type_collection_id_uindex", columns={"word_id", "type", "collection_id", "user_id"})}, indexes={@ORM\Index(name="training_type_index", columns={"type"}), @ORM\Index(name="training_word_id_index", columns={"word_id"}), @ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="training_next_index", columns={"next"})})
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
     * @ORM\ManyToOne(targetEntity="RepeatBot\Core\ORM\Entities\Word")
     * @ORM\JoinColumn(name="word_id", referencedColumnName="id", nullable=true)
     */
    private Word $word;

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
     * @ORM\Column(name="status", type="string", length=0, nullable=true, options={"default"="first"})
     */
    private string $status = 'first';

    /**
     * @var Carbon
     *
     * @ORM\Column(name="next", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private Carbon $next;

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
    public function getNext(): Carbon
    {
        return $this->next;
    }

    /**
     * @param Carbon $next
     */
    public function setNext(Carbon $next): void
    {
        $this->next = $next;
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

    /**
     * @return Word
     */
    public function getWord(): Word
    {
        return $this->word;
    }

    /**
     * @param Word $word
     */
    public function setWord(Word $word): void
    {
        $this->word = $word;
    }
}
