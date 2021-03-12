<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService;

use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\OneYearService;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Repositories\TrainingRepository;

/**
 * Class GenericMessageDirectorFabric
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class GenericMessageDirectorFabric
{
    private string $text;
    private int $chatId;
    private int $messageId;
    private int $callbackQueryId;
    protected Cache $cache;
    protected TrainingRepository $trainingRepository;

    /**
     * DirectorFabric constructor.
     *
     * @param string $query
     * @param int    $chatId
     * @param int    $messageId
     * @param int    $callbackQueryId
     */
    public function __construct(
        string $query = '',
        int $chatId = 0,
        int $messageId = 0,
        int $callbackQueryId = 0
    ) {
        $this->text = $query;
        $this->chatId = $chatId;
        $this->messageId = $messageId;
        $this->callbackQueryId = $callbackQueryId;

        $config = App::getInstance()->getConfig();
        $this->cache = Cache::getInstance()->init($config);
        /** @psalm-suppress PropertyTypeCoercion */
        $this->trainingRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(Training::class);
    }

    /**
     * @return CommandDirector
     */
    public function getCommandDirector(): CommandDirector
    {
        $userId = $this->chatId;
        $text = $this->text;
        if ($this->itsStartNewTraining($text)) {
            $cacheCommand = $this->getStrReplaceStartCommand($text);
            $this->cache->setTrainingStatus(
                $userId,
                $cacheCommand
            );

            return $this->makeDirector('translate_training');
        }

        $cacheCommand = $this->cache->checkTrainings($userId);
        if (null === $cacheCommand) {
            foreach (BotHelper::getCommands() as $name => $command) {
                if ($text === $name) {
                    return $this->makeDirector($command);
                }
            }
        }

        if ($this->isStopCommand($text)) {
            $this->cache->removeTrainings($userId, $cacheCommand);
            $this->cache->removeTrainingsStatus($userId, $cacheCommand);
            return $this->makeDirector('training');
        }

        if ($this->isDontKnow($text)) {
            $this->cache->skipTrainings($userId, $cacheCommand);
        }

        if ($this->isOneYear($text)) {
            $this->cache->saveOneYear($userId, $cacheCommand);
            /** @psalm-suppress ArgumentTypeCoercion */
            (new OneYearService($this->trainingRepository))->execute($this->cache->getTrainings($userId, $cacheCommand));
        }

        return $this->makeDirector('translate_training');
    }

    /**
     * @param string $command
     *
     * @return CommandDirector
     */
    private function makeDirector(string $command): CommandDirector
    {
        return new CommandDirector(
            new CommandOptions(
                $command,
                $this->text,
                [],
                $this->chatId
            )
        );
    }


    /**
     * @param string $text
     *
     * @return bool
     */
    private function itsStartNewTraining(string $text): bool
    {
        return in_array($this->getStrReplaceStartCommand($text), BotHelper::getTrainingTypes());
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    private function isStopCommand(string $text): bool
    {
        return $text === 'Остановить';
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
            'i dont know',
            '.',
            'p',
            'х',
        ]);
    }

    private function isOneYear(string $text): bool
    {
        return '1' === $text;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    private function getStrReplaceStartCommand(string $text): string
    {
        return str_replace(' ', '', $text);
    }
}
