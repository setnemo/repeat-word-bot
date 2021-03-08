<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\ORM\Entities\LearnNotificationPersonal;
use RepeatBot\Core\ORM\Repositories\LearnNotificationPersonalRepository;

class AlarmService extends BaseCommandService
{
    private LearnNotificationPersonalRepository $repository;

    public function __construct(CommandOptions $options)
    {
        $this->repository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(LearnNotificationPersonal::class);

        parent::__construct($options);
    }

    public function execute(): CommandInterface
    {
        $text = $this->getOptions()->getPayload();
        if ('list' === $text[0]) {
            $this->executeAlarmListCommand();
        } elseif ('reset' === $text[0]) {
            $this->executeAlarmResetCommand();
        } else {
            $this->executeSetAlarmCommand();
        }

        return $this;
    }

    private function executeAlarmListCommand(): void
    {
        $items = $this->repository->getMyAlarms($this->getOptions()->getChatId());
        $text = '';
        /** @var LearnNotificationPersonal $item */
        foreach ($items as $item) {
            $text .= strtr("(:tz) :time\n", [
                ':tz' => $item->getTimezone(),
                ':time' => $item->getAlarm()->rawFormat('H:i:s'),
            ]);
        }

        $this->setResponse(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id' => $this->getOptions()->getChatId(),
                    'text' => empty($text) ? 'Список персональных напоминаний пуст' : $text,
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'disable_notification' => 1,
                ]
            )
        );
    }

    private function executeAlarmResetCommand(): void
    {
        $this->repository->delNotifications($this->getOptions()->getChatId());
        $this->setResponse(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id' => $this->getOptions()->getChatId(),
                    'text' => 'Напоминания удалены',
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'disable_notification' => 1,
                ]
            )
        );
    }

    private function executeSetAlarmCommand(): void
    {
        $commands = $this->getOptions()->getPayload();
        $commands = array_reverse($commands);
        $time = $commands[0];
        $tz = $commands[1] ?? 'FDT';
        $this->repository->createNotification(
            $this->getOptions()->getChatId(),
            "Тренировка ждет! Начни прямо сейчас /training",
            $time,
            $tz
        );
        $text = "Напоминание на `$tz $time` создано! Посмотреть свои напоминания /alarm list";

        $this->setResponse(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id' => $this->getOptions()->getChatId(),
                    'text' => $text,
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'disable_notification' => 1,
                ]
            )
        );
    }
}
