<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\GoogleTextToSpeechService;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\Exception\EmptyVocabularyException;
use RepeatBot\Core\ORM\Entities\UserVoice;
use RepeatBot\Core\ORM\Entities\Word;

/**
 * Class VoiceEnglishCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class VoiceEnglishCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'VoiceEnglish';
    /**
     * @var string
     */
    protected $description = 'VoiceEnglish command';
    /**
     * @var string
     */
    protected $usage = '/VoiceEnglish';
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
        $config = App::getInstance()->getConfig();
        $cache = Cache::getInstance()->init($config);
        $trainingRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(Training::class);
        $wordRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(Word::class);
        $wordRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(UserVoice::class);
        $question = '';
        $type = $cache->checkTrainingsStatus($userId);
        if ($type) {
            $trainingId = $cache->getTrainings($userId, $type);
            if ($trainingId) {
                $training = $trainingRepository->getTraining($trainingId);
                $word = $training->getWord();
                $text = mb_strtolower($this->getMessage()->getText(false));
                $text = preg_replace('/(ё)/i', 'е', $text);
                $correct = match($type) {
                    'ToEnglish' => $word->getWord(),
                    'FromEnglish' => $word->getTranslate(),
                };
                $oldQuestion = match($type) {
                    'ToEnglish' => $word->getTranslate(),
                    'FromEnglish' => $word->getWord(),
                };
                $result = match($type) {
                    'ToEnglish' => $text === mb_strtolower($word->getWord()),
                    'FromEnglish' => $this->getToEnglishResult($training, $text),
                };
                if ($cache->checkSkipTrainings($userId, $type)) {
                    $cache->removeSkipTrainings($userId, $type);
                    $question = "Слово пропущено! Ответ на {$oldQuestion}: {$correct}\n\n";
                } elseif ($cache->checkOneYear($userId, $type)) {
                    $cache->removeOneYear($userId, $type);
                    $question = "Слово пропущено на 1 год! Ответ на {$oldQuestion}: {$correct}\n\n";
                } else {
                    if ($result) {
                        $trainingRepository->upStatusTraining($training);
                    }
                    $question = $result ? "Правильно!\n\n" : "Неправильно! Ответ: {$correct}\n\n";
                }
            }
            $question .= match($type) {
                'ToEnglish' => "Пожалуйста напишите ответ на английском!\n\nСлово: ",
                'FromEnglish' => "Пожалуйста напишите ответ на русском!\n\nСлово: ",
            };
        }
        try {
            $priority = $cache->getPriority($userId);
            $training = $trainingRepository->getRandomTraining($userId, $type, $priority === 1);
            $word = $training->getWord();
        } catch (EmptyVocabularyException $e) {
            $cache->removeTrainings($this->getMessage()->getFrom()->getId(), $type);
            $cache->removeTrainingsStatus($this->getMessage()->getFrom()->getId(), $type);
            try {
                $training = $trainingRepository->getNearestAvailableTrainingTime($userId, $type);
                $template = "В тренировке `:training` ближайшее слово для изучения - `:word`, ";
                $template .= "которое будет доступно `:date`. Вы всегда можете добавить новую коллекцию.";
                $text = strtr(
                    $template,
                    [
                        ':word' => $word->getWord(),
                        ':training' => $training->getType(),
                        ':date' => $training->getNext()->diffForHumans()
                    ]
                );
            } catch (EmptyVocabularyException $e) {
                $text = 'У вас нет слов для изучения. Зайдите в раздел Коллекции и добавьте себе слова для тренировок';
            }
            /** @psalm-suppress TooManyArguments */
            $keyboard = new Keyboard(...BotHelper::getTrainingKeyboard());
            $keyboard->setResizeKeyboard(true);

            $data = [
                'chat_id' => $chat_id,
                'text' => $text,
                'parse_mode' => 'markdown',
                'disable_web_page_preview' => true,
                'reply_markup' => $keyboard,
                'disable_notification' => 1,
            ];
            return Request::sendMessage($data);
        }
        $question .= match($type) {
            'ToEnglish' =>  $word->getTranslate(),
            'FromEnglish' => $word->getWord(),
        };
        $word = $training->getWord();
        $cache->setTrainingStatusId($userId, $type, $training->getId());
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getInTrainingKeyboard());
        $keyboard->setResizeKeyboard(true);
        $userVoiceRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(UserVoice::class);
        $voice = $userVoiceRepository->getRandomVoice($userId);
        if ($voice !== 'default') {
            $uri = (new GoogleTextToSpeechService($voice))->getMp3($word->getWord());
        } else {
            $uri = '/app/words/' . $word->getVoice();
        }
        $data = [
            'chat_id' => $chat_id,
            'voice' => Request::encodeFile($uri),
            'caption' => trim($question),
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ];
        return Request::sendVoice($data);
    }

    /**
     * @param Training $training
     * @param string   $text
     *
     * @return bool
     */
    private function getToEnglishResult(Training $training, string $text): bool
    {
        $result = false;
        foreach (explode('; ', mb_strtolower($training->getWord()->getTranslate())) as $translate) {
            if ($translate === trim($text)) {
                $result = true;
            }
        }

        return $result;
    }
}
