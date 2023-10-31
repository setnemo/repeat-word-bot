<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Codeception\Test\Unit;
use RepeatBot\Bot\Service\CommandService;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;
use TelegramBot\CommandWrapper\Service\EmptyCallbackService;
use UnitTester;

/**
 * Class EmptyCallbackServiceTest
 * @package Tests\Unit\Bot\Service\CommandService
 */
class EmptyCallbackServiceTest extends Unit
{
    protected UnitTester $tester;

    /**
     * @return void
     * @throws SupportTypeException
     */
    public function testEmptyCallbackCommand(): void
    {
        $chatId          = 1;
        $messageId       = 2;
        $callbackQueryId = 3;
        $command         = new CommandService(
            options: new CommandOptions(
                payload: explode('_', 'empty'),
                chatId: $chatId,
                messageId: $messageId,
                callbackQueryId: $callbackQueryId
            ),
            type: 'query'
        );

        $service = $command->makeService();
        $this->assertInstanceOf(EmptyCallbackService::class, $service);
        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector */
        $responseDirector = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector);
        $this->assertEquals('answerCallbackQuery', $responseDirector->getType());

        $this->assertEquals([
            'callback_query_id' => $callbackQueryId,
            'text'              => '',
            'show_alert'        => true,
            'cache_time'        => 3,
        ], $responseDirector->getData());
    }
}
