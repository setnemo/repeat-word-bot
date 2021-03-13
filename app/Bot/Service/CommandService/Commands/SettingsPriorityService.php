<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\UserNotification;
use RepeatBot\Core\ORM\Repositories\UserNotificationRepository;

/**
 * Class SettingsPriorityService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class SettingsPriorityService extends BaseDefaultCommandService
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
     * @throws Exception
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function execute(): CommandInterface
    {
        $userId = $this->getOptions()->getChatId();
        $priority = intval($this->getOptions()->getPayload()[2] ?? 0);
        $this->cache->setPriority($userId, $priority);
        $silent = $this->repository->getOrCreateUserNotification($userId)->getSilent();
        $data = BotHelper::editMainMenuSettings($silent, $priority, $userId, $this->getOptions()->getMessageId());

        $this->setResponse(new ResponseDirector('editMessageText', $data));

        return $this;
    }
}
