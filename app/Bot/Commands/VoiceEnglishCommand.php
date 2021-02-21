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
use RepeatBot\Core\Database\Model\Training;
use RepeatBot\Core\Database\Repository\TrainingRepository;
use RepeatBot\Core\Exception\EmptyVocabularyException;

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
        $database = Database::getInstance()->getConnection();
        $trainingRepository = new TrainingRepository($database);
        $question = '';
        $type = $cache->checkTrainingsStatus($userId);
        if ($type) {
            $trainingId = $cache->getTrainings($userId, $type);
            if ($trainingId) {
                $training = $trainingRepository->getTraining($trainingId);
                $text = mb_strtolower($this->getMessage()->getText(false));
                $text = preg_replace('/(ё)/i', 'е', $text);
                $correct = match($type) {
                    'ToEnglish' => $training->getWord(),
                    'FromEnglish' => $training->getTranslate(),
                };
                $oldQuestion = match($type) {
                    'ToEnglish' => $training->getTranslate(),
                    'FromEnglish' => $training->getWord(),
                };
                $result = match($type) {
                    'ToEnglish' => $text === $training->getWord(),
                    'FromEnglish' => $this->getToEnglishResult($training, $text),
                };
                if ($cache->checkSkipTrainings($userId, $type)) {
                    $cache->removeSkipTrainings($userId, $type);
                    $question = "Слово пропущено! Ответ на {$oldQuestion}: {$correct}\n\n";
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
            $training = $trainingRepository->getRandomTraining($userId, $type);
        } catch (EmptyVocabularyException $e) {
            $cache->removeTrainingsStatus($this->getMessage()->getFrom()->getId(), $type);
            return $this->telegram->executeCommand('Collections');
        }
        $question .= match($type) {
            'ToEnglish' =>  $training->getTranslate(),
            'FromEnglish' => $training->getWord(),
        };

        $cache->setTrainingStatusId($userId, $type, $training->getId());
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getInTrainingKeyboard());
        $keyboard->setResizeKeyboard(true);

        $uri = './words/' . $training->getVoice();
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
        foreach (explode('; ', $training->getTranslate()) as $translate) {
            if ($translate === trim($text)) {
                $result = true;
            }
        }

        return $result;
    }
}
