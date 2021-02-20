<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Database\Repository\TrainingRepository;
use RepeatBot\Core\Database\Repository\UserNotificationRepository;
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
        if ($array[0] === 'settings') {
            $silent = intval($array[2]);
            $message_id = $callback_query->getMessage()->getMessageId();
            $user_id = $callback_query->getMessage()->getChat()->getId();
            $database = Database::getInstance()->getConnection();
            $userNotificationRepository = new UserNotificationRepository($database);
            $userNotificationRepository->createOdUpdateNotification(
                $user_id,
                $silent
            );
            $symbol = $silent === 1 ? '✅' : '❌';
            $text = "Тихий режим сообщений: {$symbol}";
            $keyboard = new InlineKeyboard(BotHelper::getSettingsKeyboard($text, $silent === 1 ? 0 : 1));

            $data = [
                'chat_id'      => $user_id,
                'text' => 'В настройках можно отключить тихий режим получения уведомлений. По умолчанию тихий режим включен для всех. Для переключения режима нажмите на кнопку',
                'reply_markup' => $keyboard,
                'message_id'   => $message_id,
            ];
            Request::editMessageText($data);
        }
        if ($array[0] === 'collection') {
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
                $module = $count > 10 && $count < 15 ? ($count + 5 ) % 10 :  $count % 10 ;
                $word = match($module) {
                    1           => 'слово',
                    2,3,4,      => 'слова',
                    5,6,7,8,9,0 => 'слов',
                };
                $text = "Добавлено {$count} {$word}! Можете начать тренировку!";
                $keyboard = new Keyboard(...BotHelper::getTrainingKeyboard());
                $keyboard->setResizeKeyboard(true);
                Request::sendMessage([
                    'chat_id' => $this->getCallbackQuery()->getFrom()->getId(),
                    'text' => $text,
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'reply_markup' => $keyboard,
                ]);
            }
            $text = empty($text) ? 'Коллекция уже добавлена' : $text;
        }

        return Request::answerCallbackQuery([
            'callback_query_id' => $callback_query_id,
            'text'              => $text,
            'show_alert'        => true,
            'cache_time'        => 3,
        ]);
    }
}
