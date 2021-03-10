<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Exception;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\CommandDirector;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\OneYearService;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Repositories\TrainingRepository;

/**
 * Class MessageService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class MessageService extends BaseCommandService
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
        $payload = $this->getOptions()->getPayload();
        $text = $payload[0] ?? '';
        $cacheCommand = $this->cache->checkTrainings($userId);
        if (null === $cacheCommand && !$this->itsStartNewTraining($text)) {
            foreach (BotHelper::getCommands() as $name => $command) {
                if ($text === $name) {
                    return $this->changeDirector($command);
                }
            }
        } elseif (null !== $cacheCommand) {
            if ($this->isStopCommand($text)) {
                $this->cache->removeTrainings($userId, $cacheCommand);
                $this->cache->removeTrainingsStatus($userId, $cacheCommand);
                return $this->changeDirector('training');
            }
            if ($this->isDontKnow($text)) {
                $this->cache->skipTrainings($userId, $cacheCommand);
            }
            if ($this->isOneYear($text)) {
                $this->cache->saveOneYear($userId, $cacheCommand);
                /** @psalm-suppress PropertyTypeCoercion */
                $trainingRepository = Database::getInstance()
                    ->getEntityManager()
                    ->getRepository(Training::class);
                /** @psalm-suppress ArgumentTypeCoercion */
                (new OneYearService($trainingRepository))->execute($this->cache->getTrainings($userId, $cacheCommand));
            }
        }



        return $this;
    }

    /**
     * @param string $command
     *
     * @return CommandInterface
     */
    private function changeDirector(string $command): CommandInterface
    {
        $director = new CommandDirector(
            new CommandOptions(
                $command,
                [],
                $this->getOptions()->getChatId(),
            )
        );
        $service = $director->makeService();

        if (!$service->hasResponse()) {
            $service = $service->execute();
        }

        return $service;
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    private function itsStartNewTraining(string $text): bool
    {
        return in_array(str_replace(' ', '', $text), BotHelper::getTrainingTypes());
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
}
