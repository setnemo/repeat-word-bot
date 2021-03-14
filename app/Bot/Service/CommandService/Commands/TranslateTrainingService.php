<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Exception;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Bot\Service\GoogleTextToSpeechService;
use RepeatBot\Core\Database;
use RepeatBot\Core\Exception\EmptyVocabularyException;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Entities\UserVoice;
use RepeatBot\Core\ORM\Repositories\TrainingRepository;

/**
 * Class TranslateTrainingService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class TranslateTrainingService extends BaseDefaultCommandService
{
    private TrainingRepository $trainingRepository;

    /**
     * {@inheritDoc}
     */
    public function __construct(CommandOptions $options)
    {
        $em = Database::getInstance()->getEntityManager();
        /** @psalm-suppress PropertyTypeCoercion */
        $this->trainingRepository = $em->getRepository(Training::class);

        parent::__construct($options);
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function execute(): CommandInterface
    {
        $userId = $this->getOptions()->getChatId();
        $answer = '';
        $type = $this->cache->checkTrainingsStatus($userId);
        if (null !== $type) {
            $trainingId = $this->cache->getTrainings($userId, $type);
            if ($trainingId) {
                $answer = $this->getAnswer($trainingId, $type, $userId);
            }
            try {
                $this->newTrainingWord($type, $answer, $userId);
            } catch (EmptyVocabularyException $e) {
                $text = 'У вас нет слов для изучения. Зайдите в раздел Коллекции и добавьте себе слова для тренировок';
                try {
                    $text = $this->getNearestText($userId, $type, $answer);
                } catch (EmptyVocabularyException $e) {
                } finally {
                    $this->clearTraining($userId, $type);
                    $this->saveTextMessage($userId, $text);
                }
            }
        }

        return $this;
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

    /**
     * @return string
     */
    private function getFormattedText(): string
    {
        $text = mb_strtolower($this->getOptions()->getText());
        $text = preg_replace('/(ё)/i', 'е', $text);

        return $text;
    }

    /**
     * @param int    $trainingId
     * @param string $type
     * @param int    $userId
     *
     * @return string
     */
    private function getAnswer(int $trainingId, string $type, int $userId): string
    {
        $training = $this->trainingRepository->getTraining($trainingId);
        $word = $training->getWord();
        $text = $this->getFormattedText();
        $correct = match ($type) {
            'ToEnglish' => $word->getWord(),
            'FromEnglish' => $word->getTranslate(),
        };
        $oldQuestion = match ($type) {
            'ToEnglish' => $word->getTranslate(),
            'FromEnglish' => $word->getWord(),
        };
        $result = match ($type) {
            'ToEnglish' => $text === mb_strtolower($word->getWord()),
            'FromEnglish' => $this->getToEnglishResult($training, $text),
        };
        if ($this->cache->checkSkipTrainings($userId, $type)) {
            $this->cache->removeSkipTrainings($userId, $type);
            $text = "Слово пропущено! Ответ на {$oldQuestion}: {$correct}\n\n";
        } elseif ($this->cache->checkOneYear($userId, $type)) {
            $this->cache->removeOneYear($userId, $type);
            $text = "Слово пропущено на 1 год! Ответ на {$oldQuestion}: {$correct}\n\n";
        } else {
            if ($result) {
                $this->trainingRepository->upStatusTraining($training);
            }
            $text = $result ? "Правильно!\n\n" : "Неправильно! Ответ: {$correct}\n\n";
        }

        return $text;
    }

    /**
     * @param string $type
     * @param string      $question
     * @param int         $userId
     *
     * @throws EmptyVocabularyException
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function newTrainingWord(string $type, string $question, int $userId): void
    {
        $question .= match ($type) {
            'ToEnglish' => "Пожалуйста напишите ответ на английском!\n\nСлово: ",
            'FromEnglish' => "Пожалуйста напишите ответ на русском!\n\nСлово: ",
        };
        $priority = $this->cache->getPriority($userId);
        $training = $this->trainingRepository->getRandomTraining($userId, $type, $priority === 1);
        $word = $training->getWord();
        $question .= match ($type) {
            'ToEnglish' => $word->getTranslate(),
            'FromEnglish' => $word->getWord(),
        };
        $word = $training->getWord();
        $this->cache->setTrainingStatusId($userId, $type, $training->getId());
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getInTrainingKeyboard());
        $keyboard->setResizeKeyboard(true);
        /** @psalm-suppress PropertyTypeCoercion */
        $userVoiceRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(UserVoice::class);
        $voice = $userVoiceRepository->getRandomVoice($userId);
        $uri = GoogleTextToSpeechService::getInstance()->init($voice)->getMp3($word->getWord());
        $data = [
            'chat_id' => $userId,
            'voice' => Request::encodeFile($uri),
            'caption' => trim($question),
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ];
        $this->setResponse(new ResponseDirector('sendVoice', $data));
    }

    /**
     * @param int    $userId
     * @param string $type
     */
    private function clearTraining(int $userId, string $type): void
    {
        $this->cache->removeTrainings($userId, $type);
        $this->cache->removeTrainingsStatus($userId, $type);
    }

    /**
     * @param int    $userId
     * @param string $type
     * @param string $text
     *
     * @return string
     * @throws EmptyVocabularyException
     */
    private function getNearestText(int $userId, string $type, string $text): string
    {
        $availableTraining = $this->trainingRepository->getNearestAvailableTrainingTime($userId, $type);
        $template = "В тренировке `:training` ближайшее слово для изучения - `:word`, ";
        $template .= "которое будет доступно `:date`. Вы всегда можете добавить новую коллекцию.";

        return $text . strtr(
            $template,
            [
                ':word' => $availableTraining->getWord()->getWord(),
                ':training' => $availableTraining->getType(),
                ':date' => $availableTraining->getNext()->diffForHumans()
            ]
        );
    }

    /**
     * @param int    $userId
     * @param string $text
     *
     * @throws Exception
     */
    private function saveTextMessage(int $userId, string $text): void
    {
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getTrainingKeyboard());
        $keyboard->setResizeKeyboard(true);
        $data = [
            'chat_id' => $userId,
            'text' => $text,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ];

        $this->addStackMessage(new ResponseDirector('sendMessage', $data));
    }
}
