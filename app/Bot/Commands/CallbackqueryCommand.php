<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\App;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Database\Model\Collection;
use RepeatBot\Core\Database\Model\Word;
use RepeatBot\Core\Database\Repository\CollectionRepository;
use RepeatBot\Core\Database\Repository\TrainingRepository;
use RepeatBot\Core\Database\Repository\UserNotificationRepository;
use RepeatBot\Core\Database\Repository\WordRepository;
use RepeatBot\Core\Log;

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
        $message_id        = $callback_query->getMessage()->getMessageId();
        $user_id           = $callback_query->getMessage()->getChat()->getId();
        $database          = Database::getInstance()->getConnection();

        $text = '';
        $array = explode('_', $callback_data);
        if ($array[0] === 'settings') {
            $silent = intval($array[2]);
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
            $wordRepository = new WordRepository($database);
            $words = $wordRepository->getWordsByCollectionId(intval($array[1]));
            $trainingRepository = new TrainingRepository($database);
            if (
                !$trainingRepository->userHaveCollectionId(
                    intval($array[1]),
                    $this->getCallbackQuery()->getFrom()->getId()
                )
            ) {
                $collectionRepository = new CollectionRepository($database);
                $trainingRepository = new TrainingRepository($database);
                $allCollections = $collectionRepository->getAllPublicCollection();
                $collections = [];
                $ids = $trainingRepository->getMyCollectionIds($user_id);
                $ids[] = intval($array[1]);
                /**
                 * @var int $id
                 * @var Collection $collection */
                foreach ($allCollections as $id => $collection) {
                    if (!in_array(intval($id), $ids)) {
                        $collections[] = $collection;
                    }
                }
                $array = [[['text' => 'Все коллекции добавлены!']]];

                if (!empty($collections)) {
                    $array = BotHelper::convertCollectionToButton(
                        $collections
                    );
                }
                /** @psalm-suppress TooManyArguments */
                $keyboard = new InlineKeyboard(...$array);
                $data = [
                    'chat_id'      => $user_id,
                    'text'         => BotHelper::getCollectionText(),
                    'reply_markup' => $keyboard,
                    'message_id'   => $message_id,
                ];
                Request::editMessageText($data);

                $count = ($this->addNewWords($trainingRepository, $words, $user_id)) / 2;
                $this->progressNotify($count);
                $text = 'Коллекция добавлена.';
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

    /**
     * @param TrainingRepository $trainingRepository
     * @param array              $words
     * @param int                $userId
     *
     * @return int
     */
    public function addNewWords(TrainingRepository $trainingRepository, array $words, int $userId): int
    {
        $i = 0;
        $config = App::getInstance()->getConfig();
        $logger = Log::getInstance()->init($config)->getLogger();
        foreach (BotHelper::getTrainingTypes() as $type) {
            /** @var Word $word */
            foreach ($words as $word) {
                try {
                    $trainingRepository->createTraining(
                        $word->getId(),
                        $userId,
                        $word->getCollectionId(),
                        $type,
                        $word->getWord(),
                        $word->getTranslate(),
                        $word->getVoice()
                    );
                    ++$i;
                    if ($i % 1000 == 0) {
                        $this->progressNotify($i / 2);
                        $i = 0;
                    }
                } catch (\Throwable $t) {
                    $logger->error('addNewWords: ' . $t->getMessage(), $t->getTrace());
                }
            }
        }

        return $i;
    }

    /**
     * @param int $count
     *
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function progressNotify(int $count): void
    {
        $text = BotHelper::getAnswer('Добавлено', $count) . '!';
        $keyboard = new Keyboard(...BotHelper::getTrainingKeyboard());
        $keyboard->setResizeKeyboard(true);
        Request::sendMessage([
            'chat_id' => $this->getCallbackQuery()->getFrom()->getId(),
            'text' => $text,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ]);
    }
}
