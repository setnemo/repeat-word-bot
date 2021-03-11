<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService;

/**
 * Class CommandOptions
 * @package RepeatBot\Bot\Service\CommandService
 */
class CommandOptions
{
    protected string $command = '';
    protected array $payload = [];
    protected int $chatId = 0;
    protected int $messageId = 0;
    protected int $callbackQueryId = 0;
    
    /**
     * CommandOptions constructor.
     *
     * @param string $command
     * @param array  $payload
     * @param int    $chatId
     * @param int    $messageId
     * @param int    $callbackQueryId
     */
    public function __construct(
        string $command = '',
        array $payload = [],
        int $chatId = 0,
        int $messageId = 0,
        int $callbackQueryId = 0
    ) {
        $this->callbackQueryId = $callbackQueryId;
        $this->messageId = $messageId;
        $this->chatId = $chatId;
        $this->payload = $payload;
        $this->command = $command;
    }

    /**
     * @return int
     */
    public function getChatId(): int
    {
        return $this->chatId;
    }

    /**
     * @return int
     */
    public function getMessageId(): int
    {
        return $this->messageId;
    }

    /**
     * @return int
     */
    public function getCallbackQueryId(): int
    {
        return $this->callbackQueryId;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
