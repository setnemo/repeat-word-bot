<?php

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * Word
 *
 * @ORM\Table(name="word", uniqueConstraints={@ORM\UniqueConstraint(name="word_word_uindex", columns={"word"})})
 * @ORM\Entity(repositoryClass="RepeatBot\Core\ORM\Repositories\WordRepository")
 */
class Word
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
     * @var int
     *
     * @ORM\Column(name="collection_id", type="integer", nullable=true)
     */
    private int $collectionId;

    /**
     * @var Carbon
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private Carbon $createdAt;

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
}
