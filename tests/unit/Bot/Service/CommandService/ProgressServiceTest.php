<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Codeception\Exception\ModuleException;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManager;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Commands\ProgressService;
use RepeatBot\Bot\Service\CommandService\Messages\ProgressMessage;
use TelegramBot\CommandWrapper\ResponseDirector;
use RepeatBot\Core\ORM\Entities\Training;
use UnitTester;

/**
 * Class ProgressServiceTest
 * @package Tests\Unit\Bot\Service\CommandService
 */
class ProgressServiceTest extends Unit
{
    protected UnitTester $tester;
    protected EntityManager $em;

    /**
     * @throws ModuleException
     */
    protected function _setUp()
    {
        $this->em = $this->getModule('Doctrine2')->em;
        parent::_setUp();
    }

    public function testEmptyProgress(): void
    {
        $chatId = 42;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'progress',
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(ProgressService::class, $service);

        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => ProgressMessage::EMPTY_VOCABULARY,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ], $error->getData());
    }

    public function testHaveValidProgress(): void
    {
        $chatId = 42424242;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'progress',
                chatId: $chatId,
            )
        );
        $this->tester->addCollection($chatId);
        $trainingRepository = $this->em->getRepository(Training::class);
        $records = $trainingRepository->getMyStats($chatId);
        $text = BotHelper::getProgressText($records, '');

        $service = $command->makeService();
        $this->assertInstanceOf(ProgressService::class, $service);

        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => ProgressMessage::HINT . $text,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ], $error->getData());
    }
}
