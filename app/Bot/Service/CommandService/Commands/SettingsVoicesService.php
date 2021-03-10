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
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\ORM\Entities\UserNotification;
use RepeatBot\Core\ORM\Entities\UserVoice;
use RepeatBot\Core\ORM\Repositories\UserNotificationRepository;
use RepeatBot\Core\ORM\Repositories\UserVoiceRepository;

/**
 * Class SettingsVoicesService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class SettingsVoicesService extends BaseCommandService
{
    private UserVoiceRepository $userVoiceRepository;
    private UserNotificationRepository $userNotificationRepository;

    /**
     * {@inheritDoc}
     */
    public function __construct(CommandOptions $options)
    {
        $em =  Database::getInstance()->getEntityManager();
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
        $array = $this->getOptions()->getPayload();

        if ($array[2] === 'start') {
            $this->executeSettingsVoicesStartCommand();
        } elseif ($array[2] === 'example') {
            $this->executeSettingsVoicesExampleCommand(intval($array[3]));
        } elseif ($array[2] === 'back') {
            $this->executeSettingsVoicesBackCommand();
        } else {
            $this->executeSettingsVoicesSwitcherCommand(intval($array[2]), intval($array[3]));
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    private function executeSettingsVoicesStartCommand(): void
    {
        $userId = $this->getOptions()->getChatId();
        /** @psalm-suppress TooManyArguments */
        $keyboard = new InlineKeyboard(...BotHelper::getSettingsVoicesKeyboard(
            $this->userVoiceRepository->getFormattedVoices($userId)
        ));
        $data = [
            'chat_id' => $userId,
            'text' => BotHelper::getSettingsText(),
            'reply_markup' => $keyboard,
            'message_id' => $this->getOptions()->getMessageId(),
            'parse_mode' => 'markdown',

        ];

        $this->setResponse(new ResponseDirector('editMessageText', $data));
    }

    /**
     * @param $num
     *
     * @throws TelegramException
     * @throws Exception
     */
    private function executeSettingsVoicesExampleCommand($num): void
    {
        $userId = $this->getOptions()->getChatId();

        $this->addStackMessage(new ResponseDirector('sendVoice', [
            'chat_id' => $userId,
            'voice' => Request::encodeFile('/app/words/example/' . $num . '.mp3'),
            'caption' => 'Example ' . BotHelper::getVoices()[$num],
            'disable_notification' => 1,
        ]));

        $this->setResponse(new ResponseDirector('answerCallbackQuery', [
            'callback_query_id' => $this->getOptions()->getCallbackQueryId(),
            'text' => '',
            'show_alert' => true,
            'cache_time' => 3,
        ]));
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    private function executeSettingsVoicesBackCommand(): void
    {
        $userId = $this->getOptions()->getChatId();
        $silent = $this->userNotificationRepository->getOrCreateUserNotification(
            $userId
        )->getSilent();
        $priority = $this->cache->getPriority($userId);
        $data = BotHelper::editMainMenuSettings($silent, $priority, $userId, $this->getOptions()->getMessageId());

        $this->setResponse(new ResponseDirector('editMessageText', $data));
    }

    /**
     * @param int $num
     * @param int $switcher
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    private function executeSettingsVoicesSwitcherCommand(int $num, int $switcher): void
    {
        $userId = $this->getOptions()->getChatId();
        /** @psalm-suppress TooManyArguments */
        $keyboard = new InlineKeyboard(...BotHelper::getSettingsVoicesKeyboard(
            $this->userVoiceRepository->getFormattedVoices($userId)
        ));
        $this->userVoiceRepository->updateUserVoice($userId, BotHelper::getVoices()[$num], $switcher);
        $data = [
            'chat_id' => $userId,
            'text' => BotHelper::getSettingsText(),
            'message_id' => $this->getOptions()->getMessageId(),
            'parse_mode' => 'markdown',
            'disable_notification' => 1,
            'reply_markup' => $keyboard,
        ];
        $this->setResponse(new ResponseDirector('editMessageText', $data));
    }
}