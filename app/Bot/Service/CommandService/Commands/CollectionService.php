<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Longman\TelegramBot\Entities\InlineKeyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\ORM\Collections\WordCollection;
use RepeatBot\Core\ORM\Entities\Collection;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Entities\Word;
use RepeatBot\Core\ORM\Repositories\CollectionRepository;
use RepeatBot\Core\ORM\Repositories\TrainingRepository;
use RepeatBot\Core\ORM\Repositories\WordRepository;

class CollectionService extends BaseCommandService
{
    private CollectionRepository $collectionRepository;

    private WordRepository $wordRepository;

    private TrainingRepository $trainingRepository;

    public function __construct(CommandOptions $options)
    {
        $em = Database::getInstance()->getEntityManager();
        $this->collectionRepository = $em->getRepository(Collection::class);
        $this->wordRepository = $em->getRepository(Word::class);
        $this->trainingRepository = $em->getRepository(Training::class);

        parent::__construct($options);
    }

    public function execute(): CommandInterface
    {
        $this->executeCollectionCommand([] !== $this->getOptions()->getPayload());
        return $this;
    }

    private function executeCollectionCommand(bool $edit = false): void
    {
        if (false === $edit) {
            $this->executeFirstCollectionCommand(1);
        } else {
            $commands = $this->getOptions()->getPayload();
            $command = $commands[1] ?? '';
            $text = match($command) {
                'add' => $this->executeAddCollectionCommand(intval($commands[2])),
                'del' => $this->executeDelCollectionCommand(intval($commands[2])),
                'reset' => $this->executeResetCollectionCommand(intval($commands[2])),
            default => $this->editCollectionMessage(intval($command))
            };

                $this->setResponse(
                    new ResponseDirector(
                        'answerCallbackQuery',
                        [
                        'callback_query_id' => $this->getOptions()->getCallbackQueryId(),
                        'text'              => $text,
                        'show_alert'        => true,
                        'cache_time'        => 3,
                        ]
                    )
                );
        }
    }

    private function executeFirstCollectionCommand(int $id): void
    {
        $chatId = $this->getOptions()->getChatId();
        $this->addStackMessage(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id' => $chatId,
                    'text' => BotHelper::getCollectionText(),
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'disable_notification' => 1,
                ]
            )
        );
        $rating = $this->collectionRepository->getCollection(intval($id));
        $haveRatingWords = $this->trainingRepository->userHaveCollection(intval($id), $chatId);
        $keyboard = new InlineKeyboard(...BotHelper::getCollectionPagination($id, $haveRatingWords));
        $text = strtr("Коллекция `:name` содержит такие слова, как:\n\n`:words`", [
            ':name' => $rating->getName(),
            ':words' => implode(', ', $this->wordRepository->getExampleWords($rating->getId())),
        ]);
        $this->setResponse(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'reply_markup' => $keyboard,
                    'disable_notification' => 1,
                ]
            )
        );
    }

    private function executeAddCollectionCommand(int $id): string
    {
        $userId = $this->getOptions()->getChatId();
        $words = $this->wordRepository->getWordsByCollectionId($id);
        $this->addNewWords($this->trainingRepository, $words, $userId);
        $this->editCollectionMessage($id);

        return 'Слова добавлены';
    }

    private function executeDelCollectionCommand(int $id): string
    {
        $text = "Для удаления слов этой коллекции из вашего прогресса воспользуйтесь командой `/del collection {$id}`";
        $data = [
            'chat_id' => $this->getOptions()->getChatId(),
            'text' => $text,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ];
        $this->addStackMessage(new ResponseDirector('sendMessage', $data));
        $this->editCollectionMessage($id);

        return '';
    }

    private function executeResetCollectionCommand(int $id): string
    {
        $text = "Для сброса прогресса по словам с этой коллекции воспользуйтесь командой `/reset collection {$id}`";
        $data = [
            'chat_id' => $this->getOptions()->getChatId(),
            'text' => $text,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ];
        $this->addStackMessage(new ResponseDirector('sendMessage', $data));
        $this->editCollectionMessage($id);

        return '';
    }

    private function editCollectionMessage(int $id): string
    {
        $userId = $this->getOptions()->getChatId();
        $rating = $this->collectionRepository->getCollection(intval($id));
        $haveRatingWords = $this->trainingRepository->userHaveCollection(intval($id), $userId);
        $text = strtr("Коллекция `:name` содержит такие слова, как:\n\n`:words`", [
            ':name' => $rating->getName(),
            ':words' => implode(', ', $this->wordRepository->getExampleWords($rating->getId())),
        ]);
        $data = [
            'callback_query_id' => $this->getOptions()->getCallbackQueryId(),
            'chat_id' => $userId,
            'message_id' => $this->getOptions()->getMessageId(),
            'text' => $text,
            'parse_mode' => 'markdown',
            'reply_markup' => new InlineKeyboard(...BotHelper::getCollectionPagination($id, $haveRatingWords)),
            'disable_notification' => 1,
        ];
        $this->addStackMessage(new ResponseDirector('editMessageText', $data));

        return '';
    }

    public function addNewWords(
        TrainingRepository $trainingRepository,
        WordCollection $words,
        int $userId
    ): void {
        $i = 0;
        foreach (BotHelper::getTrainingTypes() as $type) {
            $i += $trainingRepository->bulkCreateTraining(
                $words,
                $type,
                $userId
            );
        }
        $data = [
            'chat_id' => $this->getOptions()->getChatId(),
            'text' => BotHelper::getAnswer('Добавлено ', $i / 2) . '!',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ];

        $this->addStackMessage(new ResponseDirector('sendMessage', $data));
    }
}
