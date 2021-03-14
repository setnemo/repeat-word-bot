<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Codeception\Exception\ModuleException;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManager;
use Longman\TelegramBot\Entities\Keyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Commands\ResetService;
use RepeatBot\Bot\Service\CommandService\Messages\ResetMessage;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\ORM\Entities\Training;
use UnitTester;

/**
 * Class ResetServiceTest
 * @package Tests\Unit\Bot\Service\CommandService
 */
class ResetServiceTest extends Unit
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
    public function testResetValidator(): void
    {
        $chatId = 424242;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'reset',
                payload: explode(' ', ''),
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(ResetService::class, $service);

        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => ResetMessage::ERROR_TEXT,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ], $error->getData());
    }

    public function testResetMyProgress(): void
    {
        $chatId = 424242;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'reset',
                payload: explode(' ', 'my progress'),
                chatId: $chatId,
            )
        );
        $this->tester->addCollection($chatId);
        $trainingRepository = $this->em->getRepository(Training::class);
        $trainings = $firstTrainings = $trainingRepository->findBy(['userId' => $chatId]);
        foreach ($trainings as $training) {
            $training->setStatus('never');
            $this->em->persist($training);
        }
        $this->em->flush();
        $service = $command->makeService();
        $this->assertInstanceOf(ResetService::class, $service);

        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => 'Ваш прогресс был сброшен.',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ], $error->getData());

        $trainingsAfterReset = $trainingRepository->findBy(['userId' => $chatId]);
        $this->assertEquals($firstTrainings, $trainingsAfterReset);
    }

    public function testResetMyProgressForCollection(): void
    {
        $chatId = 424242;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'reset',
                payload: explode(' ', 'collection 2'),
                chatId: $chatId,
            )
        );
        $this->tester->addCollection($chatId, 2);
        $trainingRepository = $this->em->getRepository(Training::class);
        $trainings = $firstTrainings = $trainingRepository->findBy(['userId' => $chatId]);
        foreach ($trainings as $training) {
            $training->setStatus('never');
            $this->em->persist($training);
        }
        $this->em->flush();
        $service = $command->makeService();
        $this->assertInstanceOf(ResetService::class, $service);

        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => 'Ваш прогресс по коллекции 2 был сброшен.',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ], $error->getData());

        $trainingsAfterReset = $trainingRepository->findBy(['userId' => $chatId]);
        $this->assertEquals($firstTrainings, $trainingsAfterReset);
    }
}
