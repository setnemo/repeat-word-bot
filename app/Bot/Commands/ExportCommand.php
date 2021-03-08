<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Core\App;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Metric;
use RepeatBot\Core\ORM\Entities\Export;

/**
 * Class ExportCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class ExportCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'Export';
    /**
     * @var string
     */
    protected $description = 'Export command';
    /**
     * @var string
     */
    protected $usage = '/export';
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
        $text = $this->getMessage()->getText(true);
        $config = App::getInstance()->getConfig();
        $metric = Metric::getInstance()->init($config);
        $metric->increaseMetric('usage');
        $metric->increaseMetric('export');
        $chat_id = $this->getMessage()->getChat()->getId();
        $user_id = $this->getMessage()->getFrom()->getId();
        $exportRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(Export::class);
        $array = explode(' ', $text);
        if (!$exportRepository->userHaveExport($user_id)) {
            $text = "Создание экспорта поставлено в очередь. Как только файл будет готов вы получите его в личном сообщении.";
            if (count($array) === 1 && $array[0] === '') {
                $exportRepository->create($user_id, $chat_id, 'FromEnglish');
            } elseif (
                count($array) == 2 &&
                in_array($array[0], ['FromEnglish','ToEnglish']) &&
                in_array($array[1], ['first','second','third','fourth','fifth','sixth','never'])
            ) {
                $exportRepository->create($user_id, $chat_id, $array[0] . '_' . $array[1]);
            } else {
                $text = "Допустимые форматы команды\n - /export\n - /export FromEnglish first\n" .
                    "- /export ToEnglish second\n\n Где первое слово режим без пробела, а второе название итерации. " .
                    "Посмотреть сколько у вас слов в какой итерации можно командой /progress";
            }
        } else {
            $text = "У вас есть экспорт слов, дождитесь очереди для создания файла";
        }

        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'markdown',
            'disable_notification' => 1,
        ];
        return Request::sendMessage($data);
    }
}
