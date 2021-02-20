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
                $text .= "\[{$type}] {$status} итерация: {$item['counter']} слов\n";
            }
        }
        $info = "`Подсказка:`\nFirst итерация: повтор слова через 24 часа\n";
        $info .= "Second итерация: повтор слова через 3 дня\n";
        $info .= "Third итерация: повтор слова через 7 дней\n";
        $info .= "Fourth итерация: повтор слова через 1 месяц\n";
        $info .= "Fifth итерация: повтор слова через 3 месяца\n";
        $info .= "Sixth итерация: повтор слова через 6 месяца\n";
        $info .= "Never итерация: повтор слова через 1 год\n\n";
        $info .= "`Сброс прогресса:`\nИспользуйте команду `/reset my progress`\n";
        $info .= "Будьте осторожны, сброс не обратим и вам придется начать итерации с начала\n\n";
        $info .= "`Ваша статистика:\n`";
        /** @psalm-suppress TooManyArguments */
        $data = [
            'chat_id' => $chat_id,
            'text' => $flag ? $info . $text : 'Ваш словарь пуст. Пожалуйста добавьте коллекцию!',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ];
        return Request::sendMessage($data);
    }
}
