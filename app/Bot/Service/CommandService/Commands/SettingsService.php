<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\UserNotification;
use RepeatBot\Core\ORM\Entities\UserVoice;
use RepeatBot\Core\ORM\Repositories\UserNotificationRepository;
use RepeatBot\Core\ORM\Repositories\UserVoiceRepository;

/**
 * Class SettingsService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class SettingsService extends BaseCommandService
{
    private UserVoiceRepository $userVoiceRepository;
    private UserNotificationRepository $userNotificationRepository;

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
     * @throws Exception
     */
    public function execute(): CommandInterface
    {
        $userId = $this->getOptions()->getChatId();
        $silent = $this->userNotificationRepository->getOrCreateUserNotification(
            $userId
        )->getSilent();
        $priority = $this->cache->getPriority($userId);
        $data = BotHelper::editMainMenuSettings($silent, $priority, $userId, $this->getOptions()->getMessageId());
        $this->setResponse(new ResponseDirector('sendMessage', $data));

        return $this;
    }
}
