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
use RepeatBot\Core\Database\Model\Collection;
use RepeatBot\Core\Database\Repository\CollectionRepository;
use RepeatBot\Core\Database\Repository\TrainingRepository;

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
        $database = Database::getInstance()->getConnection();
        $collectionRepository = new CollectionRepository($database);
        $trainingRepository = new TrainingRepository($database);
        $allCollections = $collectionRepository->getAllPublicCollection();
        $collections = [];
        $ids = $trainingRepository->getMyCollectionIds($chat_id);
        /**
         * @var int $id
         * @var Collection $collection */
        foreach ($allCollections as $id => $collection) {
            if (!in_array(intval($id), $ids)) {
                $collections[] = $collection;
            }
        }
        $array = [[['text' => 'Все коллекции добавлены!']]];

        if (!empty($collections)) {
            $array = BotHelper::convertCollectionToButton(
                $collections
            );
        }
        $data = [
            'chat_id' => $chat_id,
            'text' => BotHelper::getCollectionText(),
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ];
        Request::sendMessage($data);
        /** @psalm-suppress TooManyArguments */
        $keyboard = new InlineKeyboard(
            [
                ['text' => '(1)', 'callback_data' => 'rating_1001',],
                ['text' => 2, 'callback_data' => 'rating_1002',],
                ['text' => 3, 'callback_data' => 'rating_1003',],
                ['text' => 4, 'callback_data' => 'rating_1004',],
                ['text' => 5, 'callback_data' => 'rating_1005',],
                ['text' => 6, 'callback_data' => 'rating_1006',],
            ], [
                ['text' => 7, 'callback_data' => 'rating_1007',],
                ['text' => 8, 'callback_data' => 'rating_1008',],
                ['text' => 9, 'callback_data' => 'rating_1009',],
                ['text' => 10, 'callback_data' => 'rating_1010',],
                ['text' => 11, 'callback_data' => 'rating_1011',],
                ['text' => 12, 'callback_data' => 'rating_1012',],
            ]
        );
        $data = [
            'chat_id' => $chat_id,
            'text' => 'Коллекция Lorem ipsum',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ];
        return Request::sendMessage($data);
    }
}
