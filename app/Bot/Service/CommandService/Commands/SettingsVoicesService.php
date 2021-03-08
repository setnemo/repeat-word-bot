<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\ORM\Entities\UserNotification;
use RepeatBot\Core\ORM\Entities\UserVoice;
use RepeatBot\Core\ORM\Repositories\UserNotificationRepository;
use RepeatBot\Core\ORM\Repositories\UserVoiceRepository;

class SettingsVoicesService extends BaseCommandService
{
    private UserVoiceRepository $userVoiceRepository;
    private UserNotificationRepository $userNotificationRepository;

    public function __construct(CommandOptions $options)
    {
        $this->userVoiceRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(UserVoice::class);
        $this->userNotificationRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(UserNotification::class);
        parent::__construct($options);
    }

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

    private function executeSettingsVoicesStartCommand(): void
    {
        $userId = $this->getOptions()->getChatId();
        $data = [
            'chat_id' => $userId,
            'text' => BotHelper::getSettingsText(),
            'reply_markup' => new InlineKeyboard(...BotHelper::getSettingsVoicesKeyboard(
                $this->userVoiceRepository->getFormattedVoices($userId)
            )),
            'message_id' => $this->getOptions()->getMessageId(),
            'parse_mode' => 'markdown',

        ];

        $this->setResponse(new ResponseDirector('editMessageText', $data));
    }

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

    private function executeSettingsVoicesSwitcherCommand(int $num, int $switcher): void
    {
        $userId = $this->getOptions()->getChatId();

        $this->userVoiceRepository->updateUserVoice($userId, BotHelper::getVoices()[$num], $switcher);
        $data = [
            'chat_id' => $userId,
            'text' => BotHelper::getSettingsText(),
            'message_id' => $this->getOptions()->getMessageId(),
            'parse_mode' => 'markdown',
            'disable_notification' => 1,
            'reply_markup' => new InlineKeyboard(...BotHelper::getSettingsVoicesKeyboard(
                $this->userVoiceRepository->getFormattedVoices($userId)
            )),
        ];
        $this->setResponse(new ResponseDirector('editMessageText', $data));
    }
}
