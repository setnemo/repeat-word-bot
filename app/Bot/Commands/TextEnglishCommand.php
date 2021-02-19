<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use JetBrains\PhpStorm\Pure;
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
 * Class FromEnglishCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class TextEnglishCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'TextEnglish';
    /**
     * @var string
     */
    protected $description = 'TextEnglish command';
    /**
     * @var string
     */
    protected $usage = '/TextEnglish';
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
                $correct = match($type) {
                    'ToEnglish' => $training->getWord(),
                    'FromEnglish' => $training->getTranslate(),
                };
                $result = match($type) {
                    'ToEnglish' => $training->getWord() === $text,
                    'FromEnglish' => $this->getToEnglishResult($training, $text),
                };
                if ($cache->checkSkipTrainings($userId, $type)) {
                    $cache->removeSkipTrainings($userId, $type);
                    $question = "Skip word! Correct: {$correct}\n\n";
                } else {
                    if ($result) {
                        $trainingRepository->upStatusTraining($training);
                    }
                    $question = $result ? "*Correct!*\n\n" : "Incorrect! Correct: *{$correct}*\n\n";
                }
            }
            $question .= "Next word:\n";
        }
        try {
            $training = $trainingRepository->getRandomTraining($userId, $type);
        } catch (EmptyVocabularyException $e) {
            $cache->removeTrainingsStatus($this->getMessage()->getFrom()->getId(), $type);
            return $this->telegram->executeCommand('Collections');
        }
        $question .= match($type) {
            'ToEnglish' => '```' . $training->getTranslate() . '```',
            'FromEnglish' => '```' . $training->getWord() . '```',
        };
        $cache->setTrainingStatusId($userId, $type, $training->getId());
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getInTrainingKeyboard());
        $keyboard->setResizeKeyboard(true);
        $data = [
            'chat_id' => $chat_id,
            'text' => trim($question),
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
        ];
        return Request::sendMessage($data);
    }

    /**
     * @param Training $training
     * @param string   $text
     *
     * @return bool
     */
    #[Pure] private function getToEnglishResult(Training $training, string $text): bool
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
