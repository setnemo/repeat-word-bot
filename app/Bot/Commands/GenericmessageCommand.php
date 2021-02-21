<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Entities\ServerResponse;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache;
use RepeatBot\Core\Metric;

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
        $metric = Metric::getInstance()->init($config);
        $metric->increaseMetric('usage');
        $cache = Cache::getInstance()->init($config);
        $command = $cache->checkTrainings($this->getMessage()->getFrom()->getId());
        if (in_array($text, ['From English', 'To English'])) {
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
        if ($text === 'Остановить') {
            $cache->removeTrainings($this->getMessage()->getFrom()->getId(), $command);
            $cache->removeTrainingsStatus($this->getMessage()->getFrom()->getId(), $command);
            return $this->telegram->executeCommand('StartTraining');
        }
        if ($this->isDontKnow($text)) {
            $cache->skipTrainings($this->getMessage()->getFrom()->getId(), $command);
        }
        if ($command === 'FromEnglish' || $command === 'ToEnglish') {
            $command = 'VoiceEnglish';
        }

        return $this->telegram->executeCommand((string)$command);
    }
    
    /**
     * @param string $text
     *
     * @return bool
     */
    private function isDontKnow(string $text): bool
    {
        return in_array(mb_strtolower($text), [
            'я не знаю',
            'не знаю',
            'i don’t know',
            'i don\'t know',
            'don’t know',
            'don\'t know',
            'dont know',
            '.',
            'p',
            'х',
        ]);
    }
}
