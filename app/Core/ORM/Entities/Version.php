<?php

namespace RepeatBot\Core\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Version
 *
 * @ORM\Table(name="version", indexes={@ORM\Index(name="version_created_at_index", columns={"created_at"})})
 * @ORM\Entity
 */
class Version
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
     * @ORM\Column(name="version", type="string", length=12, nullable=false)
     */
    private $version;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    private $description;

    /**
     * @var int|null
     *
     * @ORM\Column(name="used", type="integer", nullable=true)
     */
    private $used = '0';

    /**
     * @var carbon|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';


}
