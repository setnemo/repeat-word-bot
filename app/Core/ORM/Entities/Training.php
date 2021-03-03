<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Training
 *
 * @ORM\Table(name="training", uniqueConstraints={@ORM\UniqueConstraint(name="training_word_type_collection_id_uindex", columns={"word", "type", "collection_id", "user_id"})}, indexes={@ORM\Index(name="repeat", columns={"repeat"}), @ORM\Index(name="training_word_id_index", columns={"word_id"}), @ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="training_type_index", columns={"type"})})
 * @ORM\Entity
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
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="word_id", type="integer", nullable=false)
     */
    private $wordId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="collection_id", type="integer", nullable=false)
     */
    private $collectionId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type;

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
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=0, nullable=true, options={"default"="first"})
     */
    private $status = 'first';

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="repeat", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $repeat = 'CURRENT_TIMESTAMP';

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $updatedAt = 'CURRENT_TIMESTAMP';


}
