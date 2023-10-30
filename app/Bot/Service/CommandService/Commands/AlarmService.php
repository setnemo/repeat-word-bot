<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\LearnNotificationPersonal;
use RepeatBot\Core\ORM\Repositories\LearnNotificationPersonalRepository;
use TelegramBot\CommandWrapper\Command\CommandInterface;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;

/**
 * Class AlarmService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class AlarmService extends BaseDefaultCommandService
{
    protected LearnNotificationPersonalRepository $repository;

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
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws SupportTypeException
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
     * @throws SupportTypeException
     */
    protected function executeAlarmListCommand(): void
    {
        $items = $this->repository->getMyAlarms($this->getOptions()->getChatId());
        $text  = '';
        /** @var LearnNotificationPersonal $item */
        foreach ($items as $item) {
            $text .= strtr("(:tz) :time\n", [
                ':tz'   => $item->getTimezone(),
                ':time' => $item->getAlarm()->rawFormat('H:i:s'),
            ]);
        }

        $this->setResponse(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id'                  => $this->getOptions()->getChatId(),
                    'text'                     => empty($text) ? 'Список персональних нагадувань порожній' : $text,
                    'parse_mode'               => 'markdown',
                    'disable_web_page_preview' => true,
                    'disable_notification'     => 1,
                ]
            )
        );
    }

    /**
     * @throws SupportTypeException
     */
    protected function executeAlarmResetCommand(): void
    {
        $this->repository->delNotifications($this->getOptions()->getChatId());
        $this->setResponse(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id'                  => $this->getOptions()->getChatId(),
                    'text'                     => 'Нагадування видалено',
                    'parse_mode'               => 'markdown',
                    'disable_web_page_preview' => true,
                    'disable_notification'     => 1,
                ]
            )
        );
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws SupportTypeException
     */
    protected function executeSetAlarmCommand(): void
    {
        $commands = $this->getOptions()->getPayload();
        $commands = array_reverse($commands);
        $time     = $commands[0];
        $tz       = $commands[1] ?? 'FDT';
        $this->repository->createNotification(
            $this->getOptions()->getChatId(),
            "Тренування чекає! Почни прямо зараз /training",
            $time,
            $tz
        );
        $text = "Нагадування на `$tz $time` створено! Переглянути свої нагадування /alarm list";

        $this->setResponse(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id'                  => $this->getOptions()->getChatId(),
                    'text'                     => $text,
                    'parse_mode'               => 'markdown',
                    'disable_web_page_preview' => true,
                    'disable_notification'     => 1,
                ]
            )
        );
    }
}
