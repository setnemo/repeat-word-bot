<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Carbon\Carbon;
use Codeception\Test\Unit;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\Commands\AlarmService;
use RepeatBot\Bot\Service\CommandService\Messages\AlarmMessage;
use RepeatBot\Core\ORM\Entities\LearnNotificationPersonal;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;
use UnitTester;

/**
 * Class AlarmServiceTest
 * @package Tests\Unit\Bot\Service\CommandService
 */
class AlarmServiceTest extends Unit
{
    protected UnitTester $tester;

    /**
     * @return void
     */
    public function testAlarmValidator(): void
    {
        $chatId  = 42;
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
            'chat_id'                  => $chatId,
            'text'                     => AlarmMessage::ERROR_TEXT,
            'parse_mode'               => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification'     => 1,
        ], $error->getData());
    }

    /**
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws SupportTypeException
     */
    public function testAlarmListEmpty(): void
    {
        $chatId  = 42;
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
            'chat_id'                  => $chatId,
            'text'                     => 'Список персональних нагадувань порожній',
            'parse_mode'               => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification'     => 1,
        ], $error->getData());
    }

    /**
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws SupportTypeException
     */
    public function testAlarmListNonEmpty(): void
    {
        $chatId  = 42;
        $time    = '01:00:00';
        $alarm   = Carbon::createFromFormat('H:m:i', $time);
        $message = 'message';
        $tz      = 'FDT';
        $created = Carbon::now();
        $updated = Carbon::now();

        $entity = new LearnNotificationPersonal();
        $entity->setUserId($chatId);
        $entity->setMessage($message);
        $entity->setTimezone($tz);
        $entity->setAlarm($alarm);
        $entity->setCreatedAt($created);
        $entity->setUpdatedAt($updated);

        $this->tester->haveLearnNotificationPersonalEntity($entity);

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
            'chat_id'                  => $chatId,
            'text'                     => "({$tz}) {$time}\n",
            'parse_mode'               => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification'     => 1,
        ], $responseDirector->getData());
    }

    /**
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws SupportTypeException
     */
    public function testAlarmCreate(): void
    {
        $chatId = 42;
        $time   = '01:00:00';
        $tz     = 'FDT';

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
            'chat_id'                  => $chatId,
            'text'                     => "Нагадування на `{$tz} {$time}` створено! Переглянути свої нагадування /alarm list",
            'parse_mode'               => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification'     => 1,
        ], $responseDirector->getData());
    }

    /**
     * @return void
     */
    public function testAlarmCreateBadTime(): void
    {
        $chatId = 42;
        $time   = '27:00:00';
        $tz     = 'FDT';

        $command = new CommandService(
            options: new CommandOptions(
                command: 'alarm',
                payload: explode(' ', "{$tz} {$time}"),
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
            'chat_id'                  => $chatId,
            'text'                     => AlarmMessage::ERROR_TEXT,
            'parse_mode'               => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification'     => 1,
        ], $responseDirector->getData());
    }

    /**
     * @return void
     */
    public function testAlarmCreateBadTz(): void
    {
        $chatId = 42;
        $time   = '01:01:01';
        $tz     = 'ASS';

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
            'chat_id'                  => $chatId,
            'text'                     => AlarmMessage::ERROR_TEXT,
            'parse_mode'               => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification'     => 1,
        ], $responseDirector->getData());
    }
}
