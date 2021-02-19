<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Database\Repository\TrainingRepository;

/**
 * Class MyVocabularyCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class MyVocabularyCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'MyVocabulary';
    /**
     * @var string
     */
    protected $description = 'MyVocabulary command';
    /**
     * @var string
     */
    protected $usage = '/MyVocabularyCommand';
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
        $database = Database::getInstance()->getConnection();
        $trainingRepository = new TrainingRepository($database);
        $records = $trainingRepository->getMyStats($userId);
        $text = '';
        $flag = false;
        foreach ($records as $type => $items) {
            foreach ($items as $item) {
                $flag = true;
                $status = ucfirst($item['status']);
                $text .= "\[{$type}] {$status} iteration: {$item['counter']} words\n";
            }
        }
        $info = "`Hint:`\nFirst iteration: repeat after 24 hours\n";
        $info .= "Second iteration: repeat after 3 days\n";
        $info .= "Third iteration: repeat after 7 days\n";
        $info .= "Fourth iteration: repeat after 1 month\n";
        $info .= "Fifth iteration: repeat after 3 months\n";
        $info .= "Sixth iteration: repeat after 6 months\n";
        $info .= "Never iteration: repeat after 1 year\n\n";
        $info .= "`Reset progress:`\nFor reset your progress use command `/reset my progress`\n";
        $info .= "Be careful - this will delete all your progress\n\n";
        $info .= "`Your stats:\n`";
        /** @psalm-suppress TooManyArguments */
        $data = [
            'chat_id' => $chat_id,
            'text' => $flag ? $info . $text : 'You vocabulary is empty. Please choose Collection and start training',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
        ];
        return Request::sendMessage($data);
    }
}
