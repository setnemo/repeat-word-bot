<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService;

use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\OneYearService;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Repositories\TrainingRepository;
use TelegramBot\CommandWrapper\Command\CommandOptions;

/**
 * Class GenericMessageDirectorFabric
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class GenericMessageDirectorFabric
{
    protected Cache $cache;
    protected TrainingRepository $trainingRepository;
    protected CommandOptions $options;

    /**
     * DirectorFabric constructor.
     *
     * @param CommandOptions $options
     */
    public function __construct(CommandOptions $options)
    {
        $this->options = $options;
        $config        = App::getInstance()->getConfig();
        $this->cache   = Cache::getInstance()->init($config);
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
        $userId = $this->getOptions()->getChatId();
        $text   = $this->getOptions()->getText();
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
        } else {
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
        }

        return $this->makeDirector('translate_training');
    }

    /**
     * @return CommandOptions
     */
    public function getOptions(): CommandOptions
    {
        return $this->options;
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
     * @return string
     */
    private function getStrReplaceStartCommand(string $text): string
    {
        return str_replace(' ', '', $text);
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
                command: $command,
                text: $this->getOptions()->getText(),
                chatId: $this->getOptions()->getChatId()
            )
        );
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    private function isStopCommand(string $text): bool
    {
        return $text === 'Зупинити';
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
}
