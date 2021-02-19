<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database\Model;

use RepeatBot\Core\Database\BaseModel;

/**
 * Class VersionNotification
 * @package RepeatBot\Core\Database\Model
 */
class VersionNotification extends BaseModel
{
    /**
     * @var int
     */
    protected int $chat_id;

    /**
     * @var int
     */
    protected int $version_id;

    /**
     * @var string
     */
    protected string $created_at;

    /**
     * @return int
     */
    public function getChatId(): int
    {
        return $this->chat_id;
    }

    /**
     * @return int
     */
    public function getVersionId(): int
    {
        return $this->version_id;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }
}
