<?php

declare(strict_types=1);

namespace Tests\Unit\Bot\Service\CommandService;

use Carbon\Carbon;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Longman\TelegramBot\Entities\Keyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Commands\DelService;
use RepeatBot\Bot\Service\CommandService\Commands\TrainingService;
use RepeatBot\Bot\Service\CommandService\Commands\TranslateTrainingService;
use RepeatBot\Bot\Service\CommandService\Messages\TrainingMessage;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Bot\Service\GoogleTextToSpeechService;
use RepeatBot\Core\Cache;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Entities\Word;
use UnitTester;

/**
 * Class TranslateTrainingServiceTest
 * @package Tests\Unit\Bot\Service\CommandService
 */
class TranslateTrainingServiceTest extends Unit
{
    protected UnitTester $tester;
    protected EntityManager $em;
    protected Cache $cache;
    private int $chatId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->chatId = 42;
        $this->em = $this->getModule('Doctrine2')->em;
        $this->cache = $this->tester->getCache();
        $this->tester->createFakeMp3s();
        $this->tester->addCollection($this->chatId);
        GoogleTextToSpeechService::getInstance()->init(
            '',
            '',
            $this->createMock(TextToSpeechClient::class),
            $this->createMock(VoiceSelectionParams::class),
            $this->createMock(AudioConfig::class),
            $this->createMock(SynthesisInput::class),
            true,
        );
    }

    protected function _after()
    {
        $this->tester->removeFakeMp3s();
    }

    /**
     * @param array $example
     * @dataProvider trainingProvider
     */
    public function testTraining(array $example): void
    {
        $command = $this->makeTraining($example['text'], true);
        $service = $command->makeService();
        $this->assertInstanceOf(TranslateTrainingService::class, $service);
        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $data */
        $data = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $data);
        $this->assertEquals('sendVoice', $data->getType());
        $payload = $data->getData();
        foreach (['chat_id', 'voice', 'caption', 'disable_notification'] as $key) {
            $this->assertArrayHasKey($key, $payload);
        }
        $question = $this->getQuestionFromCaption(
            $payload['caption']
        );
        $wordRepository = $this->em->getRepository(Word::class);
        if ($example['word']) {
            $word = $wordRepository->findOneBy(['translate' => $question]);
            $answer = $word->getWord();
        } else {
            $word = $wordRepository->findOneBy(['word' => $question]);
            $translates = $word->getTranslate();
            $answer = trim(explode(';', $translates)[0]) ?? '';
        }
        $command = $this->makeTraining($answer);
        $service = $command->makeService();
        $this->assertInstanceOf(TranslateTrainingService::class, $service);
        $service->execute();
        $response = $service->showResponses();
        /** @var ResponseDirector $data */
        $data = $response[0];
        $this->assertInstanceOf(ResponseDirector::class, $data);
        $this->assertEquals('sendVoice', $data->getType());
        $payload = $data->getData();
        foreach (['chat_id', 'voice', 'caption', 'disable_notification'] as $key) {
            $this->assertArrayHasKey($key, $payload);
        }
        $this->tester->assertStringContainsString('Правильно!', $payload['caption']);
    }

    /**
     * @param array $example
     * @dataProvider skipProvider
     */
    public function testSkip(array $example): void
    {
        $this->makeTraining('From English', true)->makeService();
        $command = $this->makeTraining($example['text']);
        $service = $command->makeService();
        $this->assertInstanceOf(TranslateTrainingService::class, $service);
        $service->execute();
        $service = $command->makeService();
        $service->execute();
        $response = $service->showResponses();
        $data = $response[0];
        $payload = $data->getData();
        $this->tester->assertStringContainsString('Слово пропущено!', $payload['caption']);
    }

    public function testSkipOneYear(): void
    {
        $this->makeTraining('From English', true)->makeService();
        $command = $this->makeTraining('1');
        $service = $command->makeService();
        $this->assertInstanceOf(TranslateTrainingService::class, $service);
        $service->execute();
        $service = $command->makeService();
        $service->execute();
        $response = $service->showResponses();
        $data = $response[0];
        $payload = $data->getData();
        $this->tester->assertStringContainsString('Слово пропущено на 1 год!', $payload['caption']);
    }

    public function testEmptyVocabulary(): void
    {
        (new CommandService(
            options: new CommandOptions(
                command: 'del',
                payload: explode(' ', 'my progress'),
                chatId: $this->chatId,
            )
        ))->makeService()->execute();

        $service = $this->makeTraining('From English', true)->makeService();
        $this->assertInstanceOf(TranslateTrainingService::class, $service);
        $service->execute();
        $response = $service->showResponses();
        $data = $response[0];
        $payload = $data->getData();
        $this->tester->assertStringContainsString(
            'У вас нет слов для изучения. Зайдите в раздел Коллекции и добавьте себе слова для тренировок',
            $payload['text']
        );
    }

    public function testNotHaveTrainingNow(): void
    {
        $this->tester->updateAllTrainingStatuses($this->chatId, 'FromEnglish');
        $service = $this->makeTraining('From English', true)->makeService();
        $this->assertInstanceOf(TranslateTrainingService::class, $service);
        $service->execute();
        $response = $service->showResponses();
        $data = $response[0];
        $payload = $data->getData();
        $this->tester->assertStringContainsString(
            'В тренировке `FromEnglish` ближайшее слово для изучения - ',
            $payload['text']
        );
    }

    public function testStopTraining(): void
    {
        $this->makeTraining('From English', true)->makeService();
        $command = $this->makeTraining('Остановить');
        $service = $command->makeService();
        $this->assertInstanceOf(TrainingService::class, $service);
        $service->execute();
        $service = $command->makeService();
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
            'chat_id' => $this->chatId,
            'text' => TrainingMessage::CHOOSE_TEXT,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => $keyboard,
            'disable_notification' => 1,
        ], $error->getData());
    }

    private function getQuestionFromCaption(string $caption): string
    {
        return explode(
            'Слово: ',
            $caption
        )[1] ?? '';
    }

    /**
     * @param string $text
     * @param bool   $clear
     *
     * @return CommandService
     */
    private function makeTraining(string $text, bool $clear = false): CommandService
    {
        $command = new CommandService(
            options: new CommandOptions(
                command: 'translate_training',
                text: $text,
                chatId: $this->chatId,
            ),
            type: 'generic'
        );
        if ($clear) {
            foreach (BotHelper::getTrainingTypes() as $type) {
                $this->cache->removeTrainings($this->chatId, $type);
                $this->cache->removeTrainingsStatus($this->chatId, $type);
            }
        }

        return $command;
    }


    public function trainingProvider(): array
    {
        return [
            [['text' => 'From English','word' => false]],
            [['text' => 'To English','word' => true]],
        ];
    }

    public function skipProvider(): array
    {
        return [
            [['text' => 'я не знаю',]],
            [['text' => 'не знаю',]],
            [['text' => 'i don’t know',]],
            [['text' => 'i don\'t know',]],
            [['text' => 'don’t know',]],
            [['text' => 'don\'t know',]],
            [['text' => 'dont know',]],
            [['text' => 'i dont know',]],
            [['text' => '.',]],
            [['text' => 'p',]],
            [['text' => 'х',]],
        ];
    }

    /**
     * @param string $type
     * @param string $status
     *
     * @return Training
     */
    private function getTrainingEntity(string $type, string $status): Training
    {
        $word = new Word();
        $word->setWord('tmp_word');
        $word->setCollectionId(37);
        $word->setTranslate('tmp_translate');
        $this->tester->haveWordEntity($word);

        $userId = 42;
        $collectionId = 1;
        $next = Carbon::now()->addDay();
        $created = Carbon::now();
        $updated = Carbon::now();

        $entity = new Training();
        $entity->setWord($word);
        $entity->setUserId($userId);
        $entity->setCollectionId($collectionId);
        $entity->setType($type);
        $entity->setStatus($status);
        $entity->setNext($next);
        $entity->setCreatedAt($created);
        $entity->setUpdatedAt($updated);

        return $entity;
    }
}
