<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Codeception\Exception\ModuleException;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManager;
use Longman\TelegramBot\Entities\InlineKeyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsPriorityService;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsService;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsSilentService;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsVoicesService;
use RepeatBot\Core\Cache;
use RepeatBot\Core\ORM\Entities\UserNotification;
use RepeatBot\Core\ORM\Entities\UserVoice;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\ResponseDirector;
use UnitTester;

/**
 * Class SettingsServiceTest
 * @package Tests\Unit\Bot\Service\CommandService
 */
class SettingsServiceTest extends Unit
{
    protected UnitTester $tester;
    protected EntityManager $em;
    protected Cache $cache;

    public function testSettingsCommand(): void
    {
        $chatId          = 1;
        $messageId       = 2;
        $callbackQueryId = 3;
        $command         = new CommandService(
            options: new CommandOptions(
                command: 'settings',
                chatId: $chatId,
                messageId: $messageId,
                callbackQueryId: $callbackQueryId
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(SettingsService::class, $service);
        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector */
        $responseDirector = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector);
        $this->assertEquals('sendMessage', $responseDirector->getType());
        $silent   = $this->em->getRepository(UserNotification::class)->getOrCreateUserNotification(
            $chatId
        )->getSilent();
        $priority = $this->cache->getPriority($chatId);
        $data     = BotHelper::editMainMenuSettings($silent, $priority, $chatId, $messageId);
        $this->assertEquals($data, $responseDirector->getData());
    }

    public function testSettingsPriorityCommand(): void
    {
        $chatId          = 1;
        $messageId       = 2;
        $callbackQueryId = 3;
        $command         = new CommandService(
            options: new CommandOptions(
                payload: explode('_', 'settings_priority_1'),
                chatId: $chatId,
                messageId: $messageId,
                callbackQueryId: $callbackQueryId
            ),
            type: 'query'
        );

        $service = $command->makeService();
        $this->assertInstanceOf(SettingsPriorityService::class, $service);
        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector */
        $responseDirector = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector);
        $this->assertEquals('editMessageText', $responseDirector->getType());
        $silent   = $this->em->getRepository(UserNotification::class)->getOrCreateUserNotification(
            $chatId
        )->getSilent();
        $priority = $this->cache->getPriority($chatId);
        $data     = BotHelper::editMainMenuSettings($silent, $priority, $chatId, $messageId);
        $this->assertEquals($data, $responseDirector->getData());
    }

    public function testSettingsSilentCommand(): void
    {
        $chatId          = 1;
        $messageId       = 2;
        $callbackQueryId = 3;
        $command         = new CommandService(
            options: new CommandOptions(
                payload: explode('_', 'settings_silent_0'),
                chatId: $chatId,
                messageId: $messageId,
                callbackQueryId: $callbackQueryId
            ),
            type: 'query'
        );

        $service = $command->makeService();
        $this->assertInstanceOf(SettingsSilentService::class, $service);
        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector */
        $responseDirector = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector);
        $this->assertEquals('editMessageText', $responseDirector->getType());
        $silent   = $this->em->getRepository(UserNotification::class)->getOrCreateUserNotification(
            $chatId
        )->getSilent();
        $priority = $this->cache->getPriority($chatId);
        $data     = BotHelper::editMainMenuSettings($silent, $priority, $chatId, $messageId);
        $this->assertEquals($data, $responseDirector->getData());
    }

    public function testSettingsVoiceStartCommand(): void
    {
        $chatId          = 1;
        $messageId       = 2;
        $callbackQueryId = 3;
        $command         = new CommandService(
            options: new CommandOptions(
                payload: explode('_', 'settings_voices_start'),
                chatId: $chatId,
                messageId: $messageId,
                callbackQueryId: $callbackQueryId
            ),
            type: 'query'
        );

        $service = $command->makeService();
        $this->assertInstanceOf(SettingsVoicesService::class, $service);
        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector */
        $responseDirector = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector);
        $this->assertEquals('editMessageText', $responseDirector->getType());
        $keyboard = new InlineKeyboard(
            ...BotHelper::getSettingsVoicesKeyboard(
                $this->em->getRepository(UserVoice::class)->getFormattedVoices($chatId)
            )
        );
        $this->assertEquals([
            'chat_id'      => $chatId,
            'text'         => BotHelper::getSettingsText(),
            'reply_markup' => $keyboard,
            'message_id'   => $messageId,
            'parse_mode'   => 'markdown',
        ], $responseDirector->getData());
    }

    public function testSettingsVoiceExampleCommand(): void
    {
        $chatId          = 21;
        $messageId       = 2;
        $callbackQueryId = 3;
        $num             = 1;
        $command         = new CommandService(
            options: new CommandOptions(
                payload: explode('_', 'settings_voices_example_' . $num),
                chatId: $chatId,
                messageId: $messageId,
                callbackQueryId: $callbackQueryId
            ),
            type: 'query'
        );

        $service = $command->makeService();
        $this->assertInstanceOf(SettingsVoicesService::class, $service);
        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector1 */
        $responseDirector1 = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector1);
        $this->assertEquals('sendVoice', $responseDirector1->getType());
        $data = $responseDirector1->getData();
        foreach (['chat_id', 'voice', 'caption', 'disable_notification'] as $key) {
            $this->assertArrayHasKey($key, $data);
        }

        /** @var ResponseDirector $responseDirector2 */
        $responseDirector2 = $response[1];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector2);
        $this->assertEquals('answerCallbackQuery', $responseDirector2->getType());
        $this->assertEquals([
            'callback_query_id' => $callbackQueryId,
            'text'              => '',
            'show_alert'        => true,
            'cache_time'        => 3,
        ], $responseDirector2->getData());
    }

    public function testSettingsVoiceSwitcherCommand(): void
    {
        $chatId          = 1;
        $messageId       = 2;
        $callbackQueryId = 3;
        $command         = new CommandService(
            options: new CommandOptions(
                payload: explode('_', 'settings_voices_2_1'),
                chatId: $chatId,
                messageId: $messageId,
                callbackQueryId: $callbackQueryId
            ),
            type: 'query'
        );

        $service = $command->makeService();
        $this->assertInstanceOf(SettingsVoicesService::class, $service);
        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector1 */
        $responseDirector1 = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector1);
        $this->assertEquals('editMessageText', $responseDirector1->getType());
        /** @psalm-suppress TooManyArguments */
        $keyboard = new InlineKeyboard(
            ...BotHelper::getSettingsVoicesKeyboard(
                $this->em->getRepository(UserVoice::class)->getFormattedVoices($chatId)
            )
        );
        $data     = [
            'chat_id'              => $chatId,
            'text'                 => BotHelper::getSettingsText(),
            'message_id'           => $messageId,
            'parse_mode'           => 'markdown',
            'disable_notification' => 1,
            'reply_markup'         => $keyboard,
        ];
        $this->assertEquals($data, $responseDirector1->getData());

        /** @var ResponseDirector $responseDirector2 */
        $responseDirector2 = $response[1];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector2);
        $this->assertEquals('answerCallbackQuery', $responseDirector2->getType());
        $data = [
            'callback_query_id' => $callbackQueryId,
            'text'              => '',
            'show_alert'        => true,
            'cache_time'        => 3,
        ];
        $this->assertEquals($data, $responseDirector2->getData());
    }

    public function testSettingsVoiceBackCommand(): void
    {
        $chatId          = 1;
        $messageId       = 2;
        $callbackQueryId = 3;
        $command         = new CommandService(
            options: new CommandOptions(
                payload: explode('_', 'settings_voices_back'),
                chatId: $chatId,
                messageId: $messageId,
                callbackQueryId: $callbackQueryId
            ),
            type: 'query'
        );

        $service = $command->makeService();
        $this->assertInstanceOf(SettingsVoicesService::class, $service);
        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector */
        $responseDirector = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector);
        $this->assertEquals('editMessageText', $responseDirector->getType());
        $silent   = $this->em->getRepository(UserNotification::class)->getOrCreateUserNotification(
            $chatId
        )->getSilent();
        $priority = $this->cache->getPriority($chatId);
        $data     = BotHelper::editMainMenuSettings($silent, $priority, $chatId, $messageId);
        $this->assertEquals($data, $responseDirector->getData());
    }

    /**
     * @throws ModuleException
     */
    protected function _setUp(): void
    {
        parent::_setUp();
        $this->em    = $this->getModule('Doctrine2')->em;
        $this->cache = $this->tester->getCache();
    }
}
