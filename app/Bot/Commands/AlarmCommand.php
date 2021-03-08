<?php

declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommand;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\App;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Metric;
use RepeatBot\Core\ORM\Entities\LearnNotificationPersonal;
use RepeatBot\Core\ORM\Repositories\LearnNotificationPersonalRepository;

/**
 * Class AlarmCommand
 * @package Longman\TelegramBot\Commands\SystemCommand
 */
class AlarmCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'alarm';
    /**
     * @var string
     */
    protected $description = 'Alarm command';
    /**
     * @var string
     */
    protected $usage = '/alarm';
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
        $chat_id = $this->getMessage()->getChat()->getId();
        $user_id = $this->getMessage()->getFrom()->getId();
        $database = Database::getInstance()->getConnection();
        /** @var LearnNotificationPersonalRepository $learnNotificationPersonalRepository */
        $learnNotificationPersonalRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(LearnNotificationPersonal::class);
        $textCommand = $this->getMessage()->getText(true);
        if ($textCommand === 'list') {
            $items = $learnNotificationPersonalRepository->getMyAlarms($user_id);
            $text = '';
            /** @var LearnNotificationPersonal $item */
            foreach ($items as $item) {
                $text .= strtr("(:tz) :time\n", [
                    ':tz' => $item->getTimezone(),
                    ':time' => $item->getAlarm()->rawFormat('H:i:s'),
                ]);
            }
            $data = [
                'chat_id' => $chat_id,
                'text' => empty($text) ? 'Список персональных напоминаний пуст' : $text,
                'parse_mode' => 'markdown',
                'disable_web_page_preview' => true,
                'disable_notification' => 1,
            ];
            return Request::sendMessage($data);
        } elseif ($textCommand === 'reset') {
            $learnNotificationPersonalRepository->delNotifications($user_id);
            $data = [
                'chat_id' => $chat_id,
                'text' => 'Напоминания удалены',
                'parse_mode' => 'markdown',
                'disable_web_page_preview' => true,
                'disable_notification' => 1,
            ];
            return Request::sendMessage($data);
        }
        $commands = explode(' ', trim($textCommand));
        $commands = array_reverse($commands);
        if ($this->validate($commands)) {
            $time = $commands[0];
            $tz = $commands[1] ?? 'FDT';
            $learnNotificationPersonalRepository->createNotification(
                $user_id,
                "Тренировка ждет! Начни прямо сейчас /training",
                $time,
                $tz
            );
            $text = "Напоминание на `$tz $time` создано! Посмотреть свои напоминания /alarm list";
        } else {
            $text = "Чтобы создать напоминание на 9 утра - воспользуйтесь командой `/alarm 9:00`. ";
            $text .= "Чтобы создать напоминание на 9 вечера - воспользуйтесь командой `/alarm 21:00`.\n\n";
            $text .= "По умолчанию используется часовой пояс FLE Standard Time (Kyiv), то есть по сути команды выше на самом деле ";
            $text .= "можно отправить с кодом FDT `/alarm FDT 9:00` и результат будет тот же. Если же вам нужно получать ";
            $text .= "оповещения по другому часовому поясу, например MSK, то нужно писать так `/alarm MSK 9:00`\n\n";
            $text .= "Посмотреть все коды часовых поясов можно командой /time\n\n";
            $text .= "Посмотреть свои напоминания /alarm list\n\n";
            $text .= "Удалить свои напоминания /alarm reset\n\n";
        }
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ];
        return Request::sendMessage($data);
    }

    /**
     * @param array $commands
     *
     * @return bool
     */
    private function validate(array $commands): bool
    {
        if (empty($commands[0])) {
            return false;
        }
        if (!preg_match("/^(?:2[0-3]|[01][0-9]|[0-9]):[0-5][0-9]$/", $commands[0])) {
            return false;
        }

        $times = explode(':', $commands[0]);
        if (intval($times[0]) < 0 || intval($times[0]) > 23 || intval($times[1]) < 0 || intval($times[1]) > 59) {
            return false;
        }
        if (isset($commands[1])) {
            $abbrs = array_column(BotHelper::getTimeZones(), 'abbr');
            if (!in_array($commands[1], $abbrs)) {
                return false;
            }
        }

        return true;
    }
}
