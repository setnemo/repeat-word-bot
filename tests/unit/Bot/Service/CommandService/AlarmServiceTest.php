<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Carbon\Carbon;
use Codeception\Test\Unit;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Commands\AlarmServiceDefault;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Bot\Service\CommandService\Validators\AlarmValidator;
use RepeatBot\Bot\Service\CommandService\Validators\Messages\AlarmMessage;
use RepeatBot\Core\ORM\Entities\Version;
use UnitTester;

class AlarmServiceTest extends Unit
{
    protected UnitTester $tester;

    public function testAlarmValidator()
    {
        $options = new CommandOptions(
            command: 'alarm',
            payload: explode(' ', ''),
            chatId: 42,
        );
        $service = (new AlarmServiceDefault($options))->validate(new AlarmValidator());
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

    public function testAlarmList()
    {
//        $options = new CommandOptions(
//            command: 'alarm',
//            payload: explode(' ', 'list'),
//            chatId: 42,
//        );
//        $service = (new AlarmService($options))->validate(new AlarmValidator());
//        $response = $service->showResponses();
//        /** @var ResponseDirector $error */
//        $error = $response[0];
//        $this->assertInstanceOf(ResponseDirector::class, $error);
//        $this->assertEquals('sendMessage', $error->getType());
//        $this->assertEquals([
//            'chat_id' => 42,
//            'text' => AlarmMessage::ERROR_TEXT,
//            'parse_mode' => 'markdown',
//            'disable_web_page_preview' => true,
//            'disable_notification' => 1,
//        ], $error->getData());
    }
}
