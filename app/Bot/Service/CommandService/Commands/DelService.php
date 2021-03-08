<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Repositories\TrainingRepository;

class DelService extends BaseCommandService
{
    private TrainingRepository $trainingRepository;

    public function __construct(CommandOptions $options)
    {
        $this->trainingRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(Training::class);
        parent::__construct($options);
    }

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

    private function executeDelMyProgressCommand(): void
    {
        $userId = $this->getOptions()->getChatId();
        foreach (BotHelper::getTrainingTypes() as $type) {
            $this->cache->removeTrainings($userId, $type);
            $this->cache->removeTrainingsStatus($userId, $type);
        }
        $this->trainingRepository->removeAllTrainings($userId);
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $data = [
            'chat_id' => $userId,
            'text' => 'Ваш прогресс был удалён.',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ];

        $this->setResponse(new ResponseDirector('sendMessage', $data));
    }

    private function executeDelCollectionCommand(int $num): void
    {
        $userId = $this->getOptions()->getChatId();
        foreach (BotHelper::getTrainingTypes() as $type) {
            $this->cache->removeTrainings($userId, $type);
            $this->cache->removeTrainingsStatus($userId, $type);
        }
        $this->trainingRepository->removeTrainings($userId, $num);
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $data = [
            'chat_id' => $userId,
            'text' => "Ваш прогресс  по коллекции {$num} был удалён.",
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ];

        $this->setResponse(new ResponseDirector('sendMessage', $data));
    }
}
