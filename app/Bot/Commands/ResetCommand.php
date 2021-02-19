<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Database\Repository\TrainingRepository;
use RepeatBot\Core\Database\Repository\WordRepository;

/**
 * Class ResetCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class ResetCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'Reset';
    /**
     * @var string
     */
    protected $description = 'Reset command';
    /**
     * @var string
     */
    protected $usage = '/Reset';
    /**
     * @var string
     */
    protected $version = '1.0.0';
    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $chat_id = $this->getMessage()->getChat()->getId();
        $userId = $this->getMessage()->getFrom()->getId();
        $text = trim($this->getMessage()->getText(true)) ?? '';
        $database = Database::getInstance()->getConnection();
        $trainingRepository = new TrainingRepository($database);
        if ($text === 'my progress') {
            $trainingRepository->resetTrainings($userId);
        }
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $data = [
            'chat_id' => $chat_id,
            'text' => $text === 'my progress' ?
                'You progress is removed' :
                'For reset use command `/reset my progress`',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
        ];

        return Request::sendMessage($data);
    }
}
