<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Database\Repository\TrainingRepository;
use RepeatBot\Core\Database\Repository\WordRepository;

/**
 * Class CallbackqueryCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

    /**
     * @var string
     */
    protected $version = '2.0.0';

    /**
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $callback_query    = $this->getCallbackQuery();
        $callback_query_id = $callback_query->getId();
        $callback_data     = $callback_query->getData();

        $text = '';
        $array = explode('_', $callback_data);
        $database = Database::getInstance()->getConnection();
        $wordRepository = new WordRepository($database);
        $words = $wordRepository->getWordsByCollectionId(intval($array[1]));
        $trainingRepository = new TrainingRepository($database);
        if (
            !$trainingRepository->userHaveCollectionId(
                intval($array[1]),
                $this->getCallbackQuery()->getFrom()->getId()
            )
        ) {
            $count = $trainingRepository->addNewWords($words, $this->getCallbackQuery()->getFrom()->getId());
            $text = "Добавлено {$count} слов! Можете начать тренировку!";
            $keyboard = new Keyboard(...BotHelper::getTrainingKeyboard());
            $keyboard->setResizeKeyboard(true);
            Request::sendMessage([
                    'chat_id' => $this->getMessage()->getChat()->getId(),
                    'text' => $text,
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'reply_markup' => $keyboard,
            ]);
        }
        return Request::answerCallbackQuery([
            'callback_query_id' => $callback_query_id,
            'text'              => empty($text) ? 'Коллекция уже добавлена' : $text,
            'show_alert'        => true,
            'cache_time'        => 3,
        ]);
    }
}
