<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Bot\BotHelper;
use TelegramBot\CommandWrapper\Command\CommandInterface;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\UserNotification;
use RepeatBot\Core\ORM\Repositories\UserNotificationRepository;

/**
 * Class SettingsSilentService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class SettingsSilentService extends BaseDefaultCommandService
{
    private UserNotificationRepository $repository;

    /**
     * {@inheritDoc}
     */
    public function __construct(CommandOptions $options)
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->repository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(UserNotification::class);
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
        $userId = $this->getOptions()->getChatId();
        $silent = intval($this->getOptions()->getPayload()[2] ?? 0);
        $this->repository->createOdUpdateNotification(
            $userId,
            $silent
        );
        $priority = $this->cache->getPriority($userId);
        $data = BotHelper::editMainMenuSettings($silent, $priority, $userId, $this->getOptions()->getMessageId());


        $this->setResponse(new ResponseDirector('editMessageText', $data));

        return $this;
    }
}
