<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class ResponseDirector
{
    private const SUPPORT_TYPES = [
        'sendMessage',
        'sendDocument',
        'sendVoice',
        'editMessageText',
        'answerCallbackQuery',
    ];

    /**
     * ResponseDirector constructor.
     *
     * @param string $type
     * @param array  $data
     *
     * @throws \Exception
     */
    public function __construct(private string $type, private array $data)
    {
        if (!in_array($type, self::SUPPORT_TYPES)) {
            throw new \Exception('ResponseDirector does not support this message type!');
        }
    }

    /**
     * @return ServerResponse
     * @throws TelegramException
     */
    public function getResponse(): ServerResponse
    {
        $data = $this->data;

        return match($this->type) {
            'sendMessage'         => Request::sendMessage($data),
            'sendDocument'        => Request::sendDocument($data),
            'sendVoice'           => Request::sendVoice($data),
            'editMessageText'     => Request::editMessageText($data),
            'answerCallbackQuery' => Request::answerCallbackQuery($data),
        };
    }
}
