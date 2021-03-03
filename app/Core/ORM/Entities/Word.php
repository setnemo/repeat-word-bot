<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Word
 *
 * @ORM\Table(name="word", uniqueConstraints={@ORM\UniqueConstraint(name="word_word_uindex", columns={"word"})})
 * @ORM\Entity
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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="word", type="string", length=255, nullable=false)
     */
    private $word;

    /**
     * @var string|null
     *
     * @ORM\Column(name="translate", type="text", length=0, nullable=true)
     */
    private $translate;

    /**
     * @var string
     *
     * @ORM\Column(name="voice", type="text", length=65535, nullable=false)
     */
    private $voice;

    /**
     * @var int|null
     *
     * @ORM\Column(name="collection_id", type="integer", nullable=true)
     */
    private $collectionId;

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';


}
