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
use RepeatBot\Core\Cache;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Database\Model\Collection;
use RepeatBot\Core\Database\Model\TrainingSave;
use RepeatBot\Core\Database\Model\Word;
use RepeatBot\Core\Database\Repository\CollectionRepository;
use RepeatBot\Core\Database\Repository\RatingRepository;
use RepeatBot\Core\Database\Repository\TrainingRepository;
use RepeatBot\Core\Database\Repository\TrainingSaveRepository;
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
            $config = App::getInstance()->getConfig();
            $cache = Cache::getInstance()->init($config);

            if ($array[1] === 'silent') {
                $silent = intval($array[2]);
                $userNotificationRepository = new UserNotificationRepository($database);
                $userNotificationRepository->createOdUpdateNotification(
                    $user_id,
                    $silent
                );
            } else {
                $userNotificationRepository = new UserNotificationRepository($database);
                $silent = $userNotificationRepository->getOrCreateUserNotification(
                    $user_id
                )->getSilent();
            }
            if ($array[1] === 'priority') {
                $priority = intval($array[2]);
                $cache->setPriority($user_id, $priority);
            } else {
                $priority = $cache->getPriority($user_id);
            }
            $symbolSilent = $silent === 1 ? '✅' : '❌';
            $symbolPriority = $priority === 1 ? '✅' : '❌';
            $textSilent = "Тихий режим сообщений: {$symbolSilent}";
            $texPriority = "Приоритет меньшей итерации: {$symbolPriority}";
            $keyboard = new InlineKeyboard(...BotHelper::getSettingsKeyboard(
                $textSilent,
                $texPriority,
                $silent === 1 ? 0 : 1,
                $priority === 1 ? 0 : 1,
            ));
            $data = [
                'chat_id'      => $user_id,
                'text' => BotHelper::getSettingsText(),
                'reply_markup' => $keyboard,
                'message_id'   => $message_id,
            ];
            Request::editMessageText($data);
        }
        if ($array[0] === 'ratings' && $array[1] === 'add') {
            $id = intval($array[2]);
            $wordRepository = new WordRepository($database);
            $words = $wordRepository->getWordsByCollectionId($id);
            $trainingRepository = new TrainingRepository($database);
            $trainingSaveRepository = new TrainingSaveRepository($database);
            $count = ($this->addNewWords($trainingRepository, $trainingSaveRepository, $words, $user_id)) / 2;
            if ($count > 0) {
                $this->progressNotify(intval($count));
            }
            $text = 'Слова добавлены';
            $answer = "Коллекция `:name` содержит такие слова, как:\n\n`:words`";
            $collectionRepository = new CollectionRepository($database);
            $wordRepository = new WordRepository($database);
            $trainingRepository = new TrainingRepository($database);
            $rating = $collectionRepository->getCollection(intval($id));
            $haveRatingWords = $trainingRepository->userHaveCollection(intval($id), $user_id);
            /** @psalm-suppress TooManyArguments */
            $keyboard = new InlineKeyboard(...BotHelper::getCollectionPagination($id, $haveRatingWords));
            $data = [
                'chat_id' => $user_id,
                'message_id'   => $message_id,
                'text' => strtr($answer, [
                    ':name' => $rating->getName(),
                    ':words' => implode(', ', $wordRepository->getExampleWords($rating->getId())),
                ]),
                'parse_mode' => 'markdown',
                'disable_web_page_preview' => true,
                'reply_markup' => $keyboard,
                'disable_notification' => 1,
            ];
            Request::editMessageText($data);
        }
        if ($array[0] === 'ratings' && $array[1] === 'del') {
            Request::sendMessage([
                'chat_id' => $this->getCallbackQuery()->getFrom()->getId(),
                'text' => 'Для удаления слов этой коллекции из вашего прогресса воспользуйтесь командой `/del collection ' .
                    intval($array[2]) .
                    '`',
                'parse_mode' => 'markdown',
                'disable_web_page_preview' => true,
                'disable_notification' => 1,
            ]);
        }
        if ($array[0] === 'ratings' && $array[1] === 'reset') {
            Request::sendMessage([
                'chat_id' => $this->getCallbackQuery()->getFrom()->getId(),
                'text' => 'Для сброса прогресса по словам с этой коллекции воспользуйтесь командой `/reset collection ' .
                    intval($array[2]) .
                    '`',
                'parse_mode' => 'markdown',
                'disable_web_page_preview' => true,
                'disable_notification' => 1,
            ]);
        }
        if ($array[0] === 'rating') {
            $id = intval($array[1]);
            $answer = "Коллекция `:name` содержит такие слова, как:\n\n`:words`";
            $collectionRepository = new CollectionRepository($database);
            $wordRepository = new WordRepository($database);
            $trainingRepository = new TrainingRepository($database);
            $rating = $collectionRepository->getCollection(intval($id));
            $haveRatingWords = $trainingRepository->userHaveCollection(intval($id), $user_id);
            /** @psalm-suppress TooManyArguments */
            $keyboard = new InlineKeyboard(...BotHelper::getCollectionPagination($id, $haveRatingWords));
            $data = [
                'chat_id' => $user_id,
                'message_id'   => $message_id,
                'text' => strtr($answer, [
                    ':name' => $rating->getName(),
                    ':words' => implode(', ', $wordRepository->getExampleWords($rating->getId())),
                ]),
                'parse_mode' => 'markdown',
                'disable_web_page_preview' => true,
                'reply_markup' => $keyboard,
                'disable_notification' => 1,
            ];
            return Request::editMessageText($data);
        }

        return Request::answerCallbackQuery([
            'callback_query_id' => $callback_query_id,
            'text'              => $text,
            'show_alert'        => true,
            'cache_time'        => 3,
        ]);
    }

    /**
     * @param TrainingRepository     $trainingRepository
     * @param TrainingSaveRepository $trainingSaveRepository
     * @param array                  $words
     * @param int                    $userId
     *
     * @return int
     */
    public function addNewWords(
        TrainingRepository $trainingRepository,
        TrainingSaveRepository $trainingSaveRepository,
        array $words,
        int $userId
    ): int {
        $i = 0;
        $config = App::getInstance()->getConfig();
        $logger = Log::getInstance()->init($config)->getLogger();
        $saves = $trainingSaveRepository->getTrainingSave($userId);
        foreach (BotHelper::getTrainingTypes() as $type) {
            /** @var Word $word */
            foreach ($words as $word) {
                try {
                    $wordId = $word->getId();
                    $collectionId = $word->getCollectionId();
                    $wordW = $word->getWord();
                    $translate = $word->getTranslate();
                    $voice = $word->getVoice();
                    $repeat = null;
                    $status = null;
                    if (isset($saves[$type][$word->getWord()])) {
                        /** @var TrainingSave $save */
                        $save = $saves[$type][$word->getWord()];
                        $repeat = $save->getRepeat();
                        $status = $save->getStatus();
                    }
                    $trainingRepository->createTraining(
                        $wordId,
                        $userId,
                        $collectionId,
                        $type,
                        $wordW,
                        $translate,
                        $voice,
                        $status,
                        $repeat
                    );
                    if (isset($saves[$type][$word->getWord()])) {
                        /** @var TrainingSave $save */
                        $save = $saves[$type][$word->getWord()];
                        $trainingSaveRepository->setUsed($save);
                    }
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
        $text = BotHelper::getAnswer('Добавлено ', $count) . '!';
        Request::sendMessage([
            'chat_id' => $this->getCallbackQuery()->getFrom()->getId(),
            'text' => $text,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ]);
    }
}
