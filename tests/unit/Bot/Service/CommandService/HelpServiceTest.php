<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Codeception\Test\Unit;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Commands\HelpService;
use RepeatBot\Bot\Service\CommandService\Messages\HelpMessage;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use UnitTester;

/**
 * Class HelpServiceTest
 * @package Tests\Unit\Bot\Service\CommandService
 */
class HelpServiceTest extends Unit
{
    protected UnitTester $tester;

    public function testAlarmListEmpty(): void
    {
        $chatId = 42;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'help',
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(HelpService::class, $service);

        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => HelpMessage::HELP_TEXT,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ], $error->getData());
    }
}
