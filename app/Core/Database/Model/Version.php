<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Model;

use RepeatBot\Core\Database\BaseModel;

/**
 * Class Version
 * @package RepeatBot\Core\Database\Model
 */
class Version extends BaseModel
{
    /**
     * @var int
     */
    protected int $id;

    /**
     * @var string
     */
    protected string $version;

    /**
     * @var string
     */
    protected string $description;

    /**
     * @var int
     */
    protected int $used;

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
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
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
}
