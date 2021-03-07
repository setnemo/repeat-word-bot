<?php

namespace RepeatBot\Core\ORM\Entities;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * Version
 *
 * @ORM\Table(name="version", indexes={@ORM\Index(name="version_created_at_index", columns={"created_at"})})
 * @ORM\Entity(repositoryClass="RepeatBot\Core\ORM\Repositories\VersionRepository")
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
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=12, nullable=false)
     */
    private string $version;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    private string $description;

    /**
     * @var int
     *
     * @ORM\Column(name="used", type="integer", nullable=true)
     */
    private int $used = 0;

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
    public function getUsed(): int
    {
        return $this->used;
    }
    
    /**
     * @param int $used
     */
    public function setUsed(int $used): void
    {
        $this->used = $used;
    }
    
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
    
    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    
    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
    
    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
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
