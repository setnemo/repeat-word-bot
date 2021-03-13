<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Codeception\Test\Unit;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Commands\AlarmService;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Bot\Service\CommandService\Validators\Messages\AlarmMessage;
use UnitTester;

class AlarmServiceTest extends Unit
{
    protected UnitTester $tester;

    public function testAlarmValidator()
    {
        $command = new CommandService(
            options: new CommandOptions(
                command: 'alarm',
                payload: explode(' ', ''),
                chatId: 42,
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
            'chat_id' => 42,
            'text' => AlarmMessage::ERROR_TEXT,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ], $error->getData());
    }

    public function testAlarmListEmpty()
    {
        $command = new CommandService(
            options: new CommandOptions(
                command: 'alarm',
                payload: explode(' ', 'list'),
                chatId: 42,
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
            'chat_id' => 42,
            'text' => 'Список персональных напоминаний пуст',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ], $error->getData());
    }
}
