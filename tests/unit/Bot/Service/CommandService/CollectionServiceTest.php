<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Codeception\Test\Unit;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\Commands\CollectionService;
use RepeatBot\Bot\Service\CommandService\Messages\CollectionMessage;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;
use UnitTester;

/**
 * Class CollectionServiceTest
 * @package Tests\Unit\Bot\Service\CommandService
 */
class CollectionServiceTest extends Unit
{
    protected UnitTester $tester;

    /**
     * @return void
     * @throws SupportTypeException
     */
    public function testCollectionFirstPage(): void
    {
        $chatId  = 1;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'collections',
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(CollectionService::class, $service);

        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector1 */
        $responseDirector1 = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector1);
        $this->assertEquals('sendMessage', $responseDirector1->getType());
        $this->assertEquals([
            'chat_id'                  => $chatId,
            'text'                     => CollectionMessage::COLLECTION_WELCOME_TEXT,
            'parse_mode'               => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification'     => 1,
        ], $responseDirector1->getData());
        /** @var ResponseDirector $responseDirector2 */
        $responseDirector2 = $response[1];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector2);
        $this->assertEquals('sendMessage', $responseDirector2->getType());
        $this->assertStringContainsString(
            'Колекція `Популярність 12/12 Частина 1` містить такі слова, як',
            $responseDirector2->getData()['text']
        );
    }

    /**
     * @return void
     * @throws SupportTypeException
     */
    public function testAddCollectionCommand(): void
    {
        $chatId          = 1;
        $messageId       = 2;
        $callbackQueryId = 3;
        $command         = new CommandService(
            options: new CommandOptions(
                payload: explode('_', 'collections_add_1'),
                chatId: $chatId,
                messageId: $messageId,
                callbackQueryId: $callbackQueryId
            ),
            type: 'query'
        );

        $service = $command->makeService();
        $this->assertInstanceOf(CollectionService::class, $service);

        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector1 */
        $responseDirector1 = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector1);
        $this->assertEquals('sendMessage', $responseDirector1->getType());
        $this->assertEquals([
            'chat_id'                  => $chatId,
            'text'                     => 'Додано 500 слів!',
            'parse_mode'               => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification'     => 1,
        ], $responseDirector1->getData());
        /** @var ResponseDirector $responseDirector2 */
        $responseDirector2 = $response[1];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector2);
        $this->assertEquals('editMessageText', $responseDirector2->getType());
        $this->assertStringContainsString(
            'Колекція `Популярність 12/12 Частина 1` містить такі слова, як',
            $responseDirector2->getData()['text']
        );
    }

    /**
     * @return void
     * @throws SupportTypeException
     */
    public function testResetCollectionCommand(): void
    {
        $this->queryTest(
            'reset',
            'Для скидання прогресу за словами цієї колекції скористайтесь командою `/reset collection 1`'
        );
    }

    /**
     * @param string $type
     * @param string $answerText
     * @return void
     * @throws SupportTypeException
     */
    private function queryTest(string $type, string $answerText): void
    {
        $chatId          = 1;
        $messageId       = 2;
        $callbackQueryId = 3;
        $command         = new CommandService(
            options: new CommandOptions(
                payload: explode('_', "collections_{$type}_1"),
                chatId: $chatId,
                messageId: $messageId,
                callbackQueryId: $callbackQueryId
            ),
            type: 'query'
        );

        $service = $command->makeService();
        $this->assertInstanceOf(CollectionService::class, $service);

        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $responseDirector1 */
        $responseDirector1 = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector1);
        $this->assertEquals('sendMessage', $responseDirector1->getType());
        $this->assertEquals([
            'chat_id'                  => $chatId,
            'text'                     => $answerText,
            'parse_mode'               => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification'     => 1,
        ], $responseDirector1->getData());
        /** @var ResponseDirector $responseDirector2 */
        $responseDirector2 = $response[1];
        $this->assertInstanceOf(ResponseDirector::class, $responseDirector2);
        $this->assertEquals('editMessageText', $responseDirector2->getType());
        $this->assertStringContainsString(
            'Колекція `Популярність 12/12 Частина 1` містить такі слова, як',
            $responseDirector2->getData()['text']
        );
    }

    /**
     * @return void
     * @throws SupportTypeException
     */
    public function testDelCollectionCommand(): void
    {
        $this->queryTest(
            'del',
            'Для видалення слів цієї колекції з вашого прогресу скористайтесь командою `/del collection 1`'
        );
    }
}
