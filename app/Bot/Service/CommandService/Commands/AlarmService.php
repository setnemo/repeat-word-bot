<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\LearnNotificationPersonal;
use RepeatBot\Core\ORM\Repositories\LearnNotificationPersonalRepository;

/**
 * Class AlarmService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class AlarmService extends BaseDefaultCommandService
{
    private LearnNotificationPersonalRepository $repository;

    /**
     * {@inheritDoc}
     */
    public function __construct(CommandOptions $options)
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->repository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(LearnNotificationPersonal::class);

        parent::__construct($options);
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
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

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
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
