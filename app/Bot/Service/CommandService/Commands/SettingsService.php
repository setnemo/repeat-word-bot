<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\UserNotification;
use RepeatBot\Core\ORM\Entities\UserVoice;
use RepeatBot\Core\ORM\Repositories\UserNotificationRepository;
use RepeatBot\Core\ORM\Repositories\UserVoiceRepository;
use TelegramBot\CommandWrapper\Command\CommandInterface;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;

/**
 * Class SettingsService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class SettingsService extends BaseDefaultCommandService
{
    protected UserVoiceRepository $userVoiceRepository;
    protected UserNotificationRepository $userNotificationRepository;

    /**
     * {@inheritDoc}
     */
    public function __construct(CommandOptions $options)
    {
        $em = Database::getInstance()->getEntityManager();
        /** @psalm-suppress PropertyTypeCoercion */
        $this->userVoiceRepository = $em->getRepository(UserVoice::class);
        /** @psalm-suppress PropertyTypeCoercion */
        $this->userNotificationRepository = $em->getRepository(UserNotification::class);
        parent::__construct($options);
    }

    /**
     * {@inheritDoc}
     * @throws SupportTypeException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function execute(): CommandInterface
    {
        $userId   = $this->getOptions()->getChatId();
        $silent   = $this->userNotificationRepository->getOrCreateUserNotification(
            $userId
        )->getSilent();
        $priority = $this->cache->getPriority($userId);
        $data     = BotHelper::editMainMenuSettings($silent, $priority, $userId, $this->getOptions()->getMessageId());
        $this->setResponse(new ResponseDirector('sendMessage', $data));

        return $this;
    }
}
