<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Database\Repository\CollectionRepository;
use RepeatBot\Core\Database\Repository\TrainingRepository;
use RepeatBot\Core\Database\Repository\WordRepository;

/**
 * Class CollectionsCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class CollectionsCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'Collections';
    /**
     * @var string
     */
    protected $description = 'Collections command';
    /**
     * @var string
     */
    protected $usage = '/collections';
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
        $user_id = $this->getMessage()->getFrom()->getId();
        $data = [
            'chat_id' => $chat_id,
            'text' => BotHelper::getCollectionText(),
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ];
        Request::sendMessage($data);

        $answer = "Коллекция `:name` содержит такие слова, как:\n\n`:words`";
        $id = 1;
        $database = Database::getInstance()->getConnection();
        $collectionRepository = new CollectionRepository($database);
        $wordRepository = new WordRepository($database);
        $trainingRepository = new TrainingRepository($database);
        $rating = $collectionRepository->getCollection(intval($id));
        $haveRatingWords = $trainingRepository->userHaveCollection(intval($id), $user_id);
        /** @psalm-suppress TooManyArguments */
        $keyboard = new InlineKeyboard(...BotHelper::getCollectionPagination($id, $haveRatingWords));
        $data = [
            'chat_id' => $chat_id,
            'text' => strtr($answer, [
                ':name' => $rating->getName(),
                ':words' => implode(', ', $wordRepository->getExampleWords($rating->getId())),
            ]),
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ];
        return Request::sendMessage($data);
    }
}
