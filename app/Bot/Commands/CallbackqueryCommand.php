<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Metric;
use RepeatBot\Core\ORM\Collections\WordCollection;
use RepeatBot\Core\ORM\Entities\Collection;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Entities\UserNotification;
use RepeatBot\Core\ORM\Entities\UserVoice;
use RepeatBot\Core\ORM\Entities\Word;
use RepeatBot\Core\ORM\Repositories\TrainingRepository;

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
        $userId            = $callback_query->getMessage()->getChat()->getId();

        $config = App::getInstance()->getConfig();
        $metric = Metric::getInstance()->init($config);
        $metric->increaseMetric('usage');

        $text = '';
        $array = explode('_', $callback_data);
        if ($array[0] === 'settings') {
            $config = App::getInstance()->getConfig();
            $cache = Cache::getInstance()->init($config);
            $userNotificationRepository = Database::getInstance()
                ->getEntityManager()
                ->getRepository(UserNotification::class);
            if ($array[1] === 'voices') {
                $userVoiceRepository = Database::getInstance()
                    ->getEntityManager()
                    ->getRepository(UserVoice::class);
                if ($array[2] === 'start') {
                    $data = [
                        'chat_id' => $userId,
                        'text' => BotHelper::getSettingsText(),
                        'reply_markup' => new InlineKeyboard(...BotHelper::getSettingsVoicesKeyboard(
                            $userVoiceRepository->getFormattedVoices($userId)
                        )),
                        'message_id' => $message_id,
                        'parse_mode' => 'markdown',
    
                    ];
                } elseif ($array[2] === 'example') {
                    $num = intval($array[3]);
                    Request::sendVoice([
                        'chat_id' => $userId,
                        'voice' => Request::encodeFile('/app/words/example/' . $num . '.mp3'),
                        'caption' => 'Example ' . BotHelper::getVoices()[$num],
                        'disable_notification' => 1,
                    ]);
                    return Request::answerCallbackQuery([
                        'callback_query_id' => $callback_query_id,
                        'text'              => $text,
                        'show_alert'        => true,
                        'cache_time'        => 3,
                    ]);
                } elseif ($array[2] === 'back') {
                    $silent = $userNotificationRepository->getOrCreateUserNotification(
                        $userId
                    )->getSilent();
                    $config = App::getInstance()->getConfig();
                    $cache = Cache::getInstance()->init($config);
                    $priority = $cache->getPriority($userId);
                    $symbolSilent = $silent === 1 ? '✅' : '❌';
                    $symbolPriority = $priority === 1 ? '✅' : '❌';
                    $textSilent = "Тихий режим сообщений: {$symbolSilent}";
                    $texPriority = "Приоритет меньших итераций: {$symbolPriority}";
                    $texVoices = "Выбрать голоса для тренировок";
                    /** @psalm-suppress TooManyArguments */
                    $keyboard = new InlineKeyboard(...BotHelper::getSettingsKeyboard(
                        $textSilent,
                        $texPriority,
                        $texVoices,
                        $silent === 1 ? 0 : 1,
                        $priority === 1 ? 0 : 1,
                    ));
                    $data = [
                        'chat_id' => $userId,
                        'text' => BotHelper::getSettingsText(),
                        'message_id' => $message_id,
                        'parse_mode' => 'markdown',
                        'disable_notification' => 1,
                        'reply_markup' => $keyboard,
                    ];
                    Request::editMessageText($data);
                } else {
                    $num = intval($array[2]);
                    $switcher = intval($array[3]);
                    $userVoiceRepository->updateUserVoice($userId, BotHelper::getVoices()[$num], $switcher);
                    $data = [
                        'chat_id' => $userId,
                        'text' => BotHelper::getSettingsText(),
                        'message_id' => $message_id,
                        'parse_mode' => 'markdown',
                        'disable_notification' => 1,
                        'reply_markup' => new InlineKeyboard(...BotHelper::getSettingsVoicesKeyboard(
                            $userVoiceRepository->getFormattedVoices($userId)
                        )),
                    ];
    
                    return Request::editMessageText($data);
    
                }
            } elseif ($array[1] === 'silent') {
                $silent = intval($array[2]);
                $userNotificationRepository->createOdUpdateNotification(
                    $userId,
                    $silent
                );
                $priority = $cache->getPriority($userId);
                $data = $this->editMainMenuSettings($silent, $priority, $userId, $message_id);
    
            } elseif ($array[1] === 'priority') {
                $priority = intval($array[2]);
                $cache->setPriority($userId, $priority);
                $silent = $userNotificationRepository->getOrCreateUserNotification(
                    $userId
                )->getSilent();
                $data = $this->editMainMenuSettings($silent, $priority, $userId, $message_id);
            }
            return Request::editMessageText($data);
        }
        if ($array[0] === 'ratings' && $array[1] === 'add') {
            $id = intval($array[2]);
            $wordRepository = Database::getInstance()
                ->getEntityManager()
                ->getRepository(Word::class);
            $words = $wordRepository->getWordsByCollectionId($id);
            $trainingRepository = Database::getInstance()
                ->getEntityManager()
                ->getRepository(Training::class);
            $this->addNewWords($trainingRepository, $words, $userId);
            $text = 'Слова добавлены';
            $answer = "Коллекция `:name` содержит такие слова, как:\n\n`:words`";
            $collectionRepository = Database::getInstance()
                ->getEntityManager()
                ->getRepository(Collection::class);
            $rating = $collectionRepository->getCollection(intval($id));
            $haveRatingWords = $trainingRepository->userHaveCollection(intval($id), $userId);
            /** @psalm-suppress TooManyArguments */
            $keyboard = new InlineKeyboard(...BotHelper::getCollectionPagination($id, $haveRatingWords));
            $data = [
                'chat_id' => $userId,
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
            $collectionRepository = Database::getInstance()
                ->getEntityManager()
                ->getRepository(Collection::class);
            $wordRepository = Database::getInstance()
                ->getEntityManager()
                ->getRepository(Word::class);
            $trainingRepository = Database::getInstance()
                ->getEntityManager()
                ->getRepository(Training::class);
            $rating = $collectionRepository->getCollection(intval($id));
            $haveRatingWords = $trainingRepository->userHaveCollection(intval($id), $userId);
            /** @psalm-suppress TooManyArguments */
            $keyboard = new InlineKeyboard(...BotHelper::getCollectionPagination($id, $haveRatingWords));
            $data = [
                'chat_id' => $userId,
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
     * @param TrainingRepository $trainingRepository
     * @param array              $words
     * @param int                $userId
     *
     * @return int
     */
    public function addNewWords(
        TrainingRepository $trainingRepository,
        WordCollection $words,
        int $userId
    ): void {
        $i = 0;
        foreach (BotHelper::getTrainingTypes() as $type) {
            $i += $trainingRepository->bulkCreateTraining(
                $words,
                $type,
                $userId
            );
        }
        $this->progressNotify($i / 2);
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
    /**
     * @param int $silent
     * @param int $priority
     * @param int $user_id
     * @param int $message_id
     * @return array
     */
    private function editMainMenuSettings(int $silent, int $priority, int $user_id, int $message_id): array
    {
        $symbolSilent = $silent === 1 ? '✅' : '❌';
        $symbolPriority = $priority === 1 ? '✅' : '❌';
        $textSilent = "Тихий режим сообщений: {$symbolSilent}";
        $texPriority = "Приоритет меньшей итерации: {$symbolPriority}";
        $texVoices = "Выбрать голоса для тренировок";
        $keyboard = new InlineKeyboard(...BotHelper::getSettingsKeyboard(
            $textSilent,
            $texPriority,
            $texVoices,
            $silent === 1 ? 0 : 1,
            $priority === 1 ? 0 : 1,
        ));
        $data = [
            'chat_id' => $user_id,
            'text' => BotHelper::getSettingsText(),
            'reply_markup' => $keyboard,
            'message_id' => $message_id,
            'parse_mode' => 'markdown',
        
        ];
        
        return $data;
    }
}
