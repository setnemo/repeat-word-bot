<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache;

class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.0.0';
    /**
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $text = $this->getMessage()->getText(false) ?? '';
        $config = App::getInstance()->getConfig();
        $cache = Cache::getInstance()->init($config);
        $command = $cache->checkTrainings($this->getMessage()->getFrom()->getId());
        if (in_array($text, ['From English', 'To English', 'Voice From English', 'Voice To English'])) {
            $cache->setTrainingStatus(
                $this->getMessage()->getFrom()->getId(),
                str_replace(' ', '', $text)
            );
        }
        if ($command === null) {
            foreach (BotHelper::getCommands() as $name => $command) {
                if ($text === $name) {
                    return $this->telegram->executeCommand($command);
                }
            }
        }
        if ($text === 'Stop Training') {
            $cache->removeTrainings($this->getMessage()->getFrom()->getId(), $command);
            $cache->removeTrainingsStatus($this->getMessage()->getFrom()->getId(), $command);
            return $this->telegram->executeCommand('StartTraining');
        }
        if ($text === 'Don\'t know this word') {
            $cache->skipTrainings($this->getMessage()->getFrom()->getId(), $command);
        }
        if ($command === 'FromEnglish' || $command === 'ToEnglish') {
            $command = 'TextEnglish';
        }
        if ($command === 'VoiceFromEnglish' || $command === 'VoiceToEnglish') {
            $command = 'VoiceEnglish';
        }

        return $this->telegram->executeCommand((string)$command);
    }
}
