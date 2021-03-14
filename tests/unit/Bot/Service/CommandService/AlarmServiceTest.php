<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Carbon\Carbon;
use Codeception\Test\Unit;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Commands\AlarmService;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Bot\Service\CommandService\Validators\Messages\AlarmMessage;
use RepeatBot\Core\ORM\Entities\LearnNotificationPersonal;
use UnitTester;

class AlarmServiceTest extends Unit
{
    protected UnitTester $tester;

    public function testAlarmValidator(): void
    {
        $chatId = 42;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'alarm',
                payload: explode(' ', ''),
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(AlarmService::class, $service);

        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => AlarmMessage::ERROR_TEXT,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ], $error->getData());
    }

    public function testAlarmListEmpty(): void
    {
        $chatId = 42;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'alarm',
                payload: explode(' ', 'list'),
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(AlarmService::class, $service);

        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => 'Список персональных напоминаний пуст',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ], $error->getData());
    }

    public function testAlarmListNonEmpty(): void
    {
        $chatId = 42;
        $time = '01:00:00';
        $alarm = Carbon::createFromFormat('H:m:i', $time);
        $message = 'message';
        $tz = 'FDT';
        $created = Carbon::now();
        $updated = Carbon::now();
    
        $entity = new LearnNotificationPersonal();
        $entity->setUserId($chatId);
        $entity->setMessage($message);
        $entity->setTimezone($tz);
        $entity->setAlarm($alarm);
        $entity->setCreatedAt($created);
        $entity->setUpdatedAt($updated);
    
        $this->tester->haveLearnNotificationPersonalInDatabase($entity);

        $command = new CommandService(
            options: new CommandOptions(
                command: 'alarm',
                payload: explode(' ', 'list'),
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(AlarmService::class, $service);

        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector */
        $responseDirector = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector);
        $this->assertEquals('sendMessage', $responseDirector->getType());
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => "({$tz}) {$time}\n",
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ], $responseDirector->getData());
    }

    public function testAlarmCreate(): void
    {
        $chatId = 42;
        $time = '01:00:00';
        $tz = 'FDT';

        $command = new CommandService(
            options: new CommandOptions(
                command: 'alarm',
                payload: explode(' ', $time),
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(AlarmService::class, $service);

        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector */
        $responseDirector = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector);
        $this->assertEquals('sendMessage', $responseDirector->getType());
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => "Напоминание на `{$tz} {$time}` создано! Посмотреть свои напоминания /alarm list",
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ], $responseDirector->getData());
    }

    public function testAlarmCreateBadTime(): void
    {
        $chatId = 42;
        $time = '27:00:00';
        $tz = 'FDT';

        $command = new CommandService(
            options: new CommandOptions(
                command: 'alarm',
                payload: explode(' ', $time),
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(AlarmService::class, $service);

        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector */
        $responseDirector = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector);
        $this->assertEquals('sendMessage', $responseDirector->getType());
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => AlarmMessage::ERROR_TEXT,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ], $responseDirector->getData());
    }

    public function testAlarmCreateBadTz(): void
    {
        $chatId = 42;
        $time = '01:01:01';
        $tz = 'ASS';

        $command = new CommandService(
            options: new CommandOptions(
                command: 'alarm',
                payload: explode(' ', $time),
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(AlarmService::class, $service);

        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector */
        $responseDirector = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector);
        $this->assertEquals('sendMessage', $responseDirector->getType());
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => AlarmMessage::ERROR_TEXT,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ], $responseDirector->getData());
    }
}
