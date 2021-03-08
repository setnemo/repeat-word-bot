<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Metric;
use RepeatBot\Core\ORM\Entities\Training;

/**
 * Class DelCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class DelCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'Del';
    /**
     * @var string
     */
    protected $description = 'Del command';
    /**
     * @var string
     */
    protected $usage = '/del';
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
        $config = App::getInstance()->getConfig();
        $metric = Metric::getInstance()->init($config);
        $metric->increaseMetric('usage');

        $flag = false;
        $chat_id = $this->getMessage()->getChat()->getId();
        $userId = $this->getMessage()->getFrom()->getId();
        $text = trim($this->getMessage()->getText(true)) ?? '';
        $trainingRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(Training::class);
        $cache = Cache::getInstance()->init($config);
        if ($text === 'my progress') {
            foreach (BotHelper::getTrainingTypes() as $type) {
                $cache->removeTrainings($this->getMessage()->getFrom()->getId(), $type);
                $cache->removeTrainingsStatus($this->getMessage()->getFrom()->getId(), $type);
            }
            $trainingRepository->removeAllTrainings($userId);
            $flag = true;
        }
        $array = explode(' ', $text);
        if ($array[0] === 'collection' && intval($array[1]) > 0 && intval($array[1]) < 37) {
            foreach (BotHelper::getTrainingTypes() as $type) {
                $cache->removeTrainings($this->getMessage()->getFrom()->getId(), $type);
                $cache->removeTrainingsStatus($this->getMessage()->getFrom()->getId(), $type);
            }
            $trainingRepository->removeTrainings($userId, intval($array[1]));
            $flag = true;
        }
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $data = [
            'chat_id' => $chat_id,
            'text' => $flag ?
                'Ваш прогресс был удалён' :
                "`Сброс прогресса:`\nИспользуйте команду `/del collection <number>` Будьте осторожны, сброс не обратим и вам придется начать итерации с начала",
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ];

        return Request::sendMessage($data);
    }
}
