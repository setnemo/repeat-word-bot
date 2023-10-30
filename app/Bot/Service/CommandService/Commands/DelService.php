<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Longman\TelegramBot\Entities\Keyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Repositories\TrainingRepository;
use TelegramBot\CommandWrapper\Command\CommandInterface;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;

/**
 * Class DelService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class DelService extends BaseDefaultCommandService
{
    private TrainingRepository $trainingRepository;

    /**
     * {@inheritDoc}
     */
    public function __construct(CommandOptions $options)
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->trainingRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(Training::class);
        parent::__construct($options);
    }

    /**
     * {@inheritDoc}
     * @throws SupportTypeException
     */
    public function execute(): CommandInterface
    {
        $array = $this->getOptions()->getPayload();

        if (['my', 'progress'] === $array) {
            $this->executeDelMyProgressCommand();
        } elseif ('collection' === $array[0]) {
            $this->executeDelCollectionCommand(intval($array[1]));
        }

        return $this;
    }

    /**
     * @throws SupportTypeException
     */
    private function executeDelMyProgressCommand(): void
    {
        $userId = $this->getOptions()->getChatId();
        foreach (BotHelper::getTrainingTypes() as $type) {
            $this->cache->removeTrainings($userId, $type);
            $this->cache->removeTrainingsStatus($userId, $type);
        }
        $this->trainingRepository->removeAllTrainings($userId);
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $data = [
            'chat_id'                  => $userId,
            'text'                     => 'Ваш прогрес був видалений.',
            'parse_mode'               => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup'             => $keyboard,
            'disable_notification'     => 1,
        ];

        $this->setResponse(new ResponseDirector('sendMessage', $data));
    }

    /**
     * @param int $num
     *
     * @throws SupportTypeException
     */
    private function executeDelCollectionCommand(int $num): void
    {
        $userId = $this->getOptions()->getChatId();
        foreach (BotHelper::getTrainingTypes() as $type) {
            $this->cache->removeTrainings($userId, $type);
            $this->cache->removeTrainingsStatus($userId, $type);
        }
        $this->trainingRepository->removeTrainings($userId, $num);
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $data = [
            'chat_id'                  => $userId,
            'text'                     => "Ваш прогрес по колекції {$num} був видалений.",
            'parse_mode'               => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup'             => $keyboard,
            'disable_notification'     => 1,
        ];

        $this->setResponse(new ResponseDirector('sendMessage', $data));
    }
}
