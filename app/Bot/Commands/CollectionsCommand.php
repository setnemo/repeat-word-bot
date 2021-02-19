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
        $collections = $collectionRepository->getAllPublicCollection();
        /** @psalm-suppress TooManyArguments */
        $keyboard = new InlineKeyboard(...BotHelper::convertCollectionToButton(
            $collections
        ));
        $data = [
            'chat_id' => $chat_id,
            'text' => 'Select and add collection to you vocabulary:',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
        ];
        return Request::sendMessage($data);
    }
}
