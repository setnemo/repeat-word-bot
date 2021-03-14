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
use RepeatBot\Bot\Service\CommandService\Commands\DelService;
use RepeatBot\Bot\Service\CommandService\Messages\DelMessage;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\Cache;
use RepeatBot\Core\ORM\Entities\Training;
use UnitTester;

/**
 * Class DelServiceTest
 * @package Tests\Unit\Bot\Service\CommandService
 */
class DelServiceTest extends Unit
{
    protected UnitTester $tester;
    protected EntityManager $em;
    protected Cache $cache;

    protected function _setUp()
    {
        parent::_setUp();
        $this->em = $this->getModule('Doctrine2')->em;
        $this->cache = $this->tester->getCache();
    }

    public function testDelValidator(): void
    {
        $chatId = 42;
        $command = new CommandService(
            options: new CommandOptions(
                command: 'del',
                payload: explode(' ', ''),
                chatId: $chatId,
            )
        );

        $service = $command->makeService();
        $this->assertInstanceOf(DelService::class, $service);

        $response = $service->showResponses();
        /** @var ResponseDirector $error */
        $error = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $error);
        $this->assertEquals('sendMessage', $error->getType());
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $this->assertEquals([
            'chat_id' => $chatId,
            'text' => DelMessage::ERROR_TEXT,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ], $error->getData());
    }

    public function testDelMyProgress(): void
    {
        $id = 4242;
        $chatId = 42;
        $type = 'FromEnglish';
        $command = new CommandService(
            options: new CommandOptions(
                command: 'del',
                payload: explode(' ', 'my progress'),
                chatId: $chatId,
            )
        );
        $this->tester->addCollection($chatId);
        $this->cache->setTrainingStatusId($chatId, $type, $id);
        $this->cache->setTrainingStatus($chatId, $type);
        $this->assertEquals($type, $this->cache->checkTrainings($chatId));
        $this->assertEquals($type, $this->cache->checkTrainingsStatus($chatId));
        $service = $command->makeService();
        $this->assertInstanceOf(DelService::class, $service);

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
            'text' => 'Ваш прогресс был удалён.',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ], $error->getData());

        $trainingRepository = $this->em->getRepository(Training::class);
        $trainings = $trainingRepository->findBy(['userId' => $chatId]);
        $this->assertEquals([], $trainings);
        $this->assertEquals(null, $this->cache->checkTrainings($chatId));
        $this->assertEquals(null, $this->cache->checkTrainingsStatus($chatId));
    }

    public function testDelCollection(): void
    {
        $id = 4242;
        $chatId = 42;
        $type = 'FromEnglish';
        $command = new CommandService(
            options: new CommandOptions(
                command: 'del',
                payload: explode(' ', 'collection 1'),
                chatId: $chatId,
            )
        );
        $this->tester->addCollection($chatId);
        $this->cache->setTrainingStatusId($chatId, $type, $id);
        $this->cache->setTrainingStatus($chatId, $type);
        $this->assertEquals($type, $this->cache->checkTrainings($chatId));
        $this->assertEquals($type, $this->cache->checkTrainingsStatus($chatId));
        $service = $command->makeService();
        $this->assertInstanceOf(DelService::class, $service);

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
            'text' => 'Ваш прогресс по коллекции 1 был удалён.',
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ], $error->getData());

        $trainingRepository = $this->em->getRepository(Training::class);
        $trainings = $trainingRepository->findBy(['userId' => $chatId]);
        $this->assertEquals([], $trainings);
        $this->assertEquals(null, $this->cache->checkTrainings($chatId));
        $this->assertEquals(null, $this->cache->checkTrainingsStatus($chatId));
    }
}
