<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Codeception\Test\Unit;
use Longman\TelegramBot\Entities\Keyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\Commands\TimeService;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;
use UnitTester;

/**
 * Class TimeServiceTest
 * @package Tests\Unit\Bot\Service\CommandService
 */
class TimeServiceTest extends Unit
{
    protected UnitTester $tester;

    /**
     * @return void
     * @throws SupportTypeException
     */
    public function testWelcome(): void
    {
        $chatId  = 42;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'time',
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(TimeService::class, $service);

        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $this->assertEquals([
            'chat_id'                  => $chatId,
            'text'                     => BotHelper::getTimeText(),
            'parse_mode'               => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup'             => $keyboard,
            'disable_notification'     => 1,
        ], $error->getData());
    }
}
