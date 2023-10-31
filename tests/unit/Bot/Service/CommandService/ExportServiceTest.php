<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Codeception\Exception\ModuleException;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\Keyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\Commands\ExportService;
use RepeatBot\Bot\Service\CommandService\Messages\ExportMessage;
use RepeatBot\Core\ORM\Entities\Export;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;
use UnitTester;

/**
 * Class ExportServiceTest
 * @package Tests\Unit\Bot\Service\CommandService
 */
class ExportServiceTest extends Unit
{
    protected UnitTester $tester;
    protected EntityManager $em;

    /**
     * @return void
     * @throws ModuleException
     */
    protected function _setUp(): void
    {
        parent::_setUp();
        $this->em = $this->getModule('Doctrine2')->em;
    }

    /**
     * @param array $example
     * @dataProvider errorProvider
     */
    public function testExportValidator(array $example): void
    {
        $chatId  = 42;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'export',
                payload: explode(' ', $example['payload']),
                chatId: $chatId,
            )
        );

        if ($example['haveExport']) {
            $entity = new Export();
            $entity->setUserId($chatId);
            $entity->setWordType('FromEnglish');
            $entity->setChatId($chatId);
            $entity->setUsed(0);
            $this->tester->haveExportEntity($entity);
        }
        $service = $command->makeService();
        $this->assertInstanceOf(ExportService::class, $service);

        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $this->assertEquals([
            'chat_id'              => $chatId,
            'text'                 => $example['message'],
            'parse_mode'           => 'markdown',
            'disable_notification' => 1,
        ], $error->getData());
    }

    /**
     * @dataProvider successProvider
     *
     * @param array $example
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws SupportTypeException
     */
    public function testExportSuccess(array $example): void
    {
        $chatId  = 42;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'export',
                payload: explode(' ', $example['payload']),
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(ExportService::class, $service);
        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $this->assertEquals([
            'chat_id'              => $chatId,
            'text'                 => ExportMessage::EXPORT_TEXT,
            'parse_mode'           => 'markdown',
            'disable_notification' => 1,
        ], $error->getData());
        $this->tester->seeInRepository(Export::class, ['chatId' => $chatId]);
    }

    /**
     * @return array[]
     */
    public function errorProvider(): array
    {
        return [
            [['payload' => '1', 'message' => ExportMessage::ERROR_INVALID_PAYLOAD_TEXT, 'haveExport' => false]],
            [['payload' => 'first', 'message' => ExportMessage::ERROR_INVALID_PAYLOAD_TEXT, 'haveExport' => false]],
            [['payload' => 'FromEnglish', 'message' => ExportMessage::ERROR_INVALID_PAYLOAD_TEXT, 'haveExport' => false]],
            [['payload' => 'FromEnglish 1', 'message' => ExportMessage::ERROR_INVALID_PAYLOAD_TEXT, 'haveExport' => false]],
            [['payload' => 'FromEnglish first', 'message' => ExportMessage::ERROR_HAVE_EXPORT_TEXT, 'haveExport' => true]],
            [['payload' => '', 'message' => ExportMessage::ERROR_HAVE_EXPORT_TEXT, 'haveExport' => true]],
        ];
    }

    /**
     * @return array[]
     */
    public function successProvider(): array
    {
        return [
            [['payload' => '']],
            [['payload' => 'FromEnglish first']],
            [['payload' => 'ToEnglish first']],
        ];
    }
}
