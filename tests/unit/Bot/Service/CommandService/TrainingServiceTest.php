<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Codeception\Test\Unit;
use Longman\TelegramBot\Entities\Keyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Commands\TrainingService;
use RepeatBot\Bot\Service\CommandService\Messages\TrainingMessage;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use UnitTester;

/**
 * Class TrainingServiceTest
 * @package Tests\Unit\Bot\Service\CommandService
 */
class TrainingServiceTest extends Unit
{
    protected UnitTester $tester;

    public function testWelcome(): void
    {
        $chatId = 42;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'training',
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(TrainingService::class, $service);

        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getTrainingKeyboard());
        $keyboard->setResizeKeyboard(true);
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => TrainingMessage::CHOOSE_TEXT,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ], $error->getData());
    }
}
