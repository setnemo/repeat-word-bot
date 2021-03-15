<?php

declare(strict_types=1);

namespace Tests\Unit\Core\ORM\Repositories;

use Carbon\Carbon;
use Codeception\Exception\ModuleException;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManager;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Repositories\TrainingRepository;
use UnitTester;

/**
 * Class TrainingRepositoryTest
 * @package Tests\Unit\Core\ORM\Repositories
 */
final class TrainingRepositoryTest extends Unit
{
    protected UnitTester $tester;
    protected EntityManager $em;
    protected TrainingRepository $trainingRepository;
    protected int $chatId;

    /**
     * @throws ModuleException
     */
    protected function _setUp()
    {
        parent::_setUp();
        $this->em = $this->getModule('Doctrine2')->em;
        $this->trainingRepository = $this->em->getRepository(Training::class);
        $this->chatId = 42;
        $this->tester->addCollection($this->chatId);
    }

    public function testUpStatusTraining(): void
    {
        $training = $this->trainingRepository->getTrainings($this->chatId, 'FromEnglish')[0];
        $training = $this->trainingRepository->upStatusTraining($training);
        $this->assertEquals('second', $training->getStatus());
        $time = Carbon::now(Database::DEFAULT_TZ)->addMinutes(24 * 60);
        $this->assertEquals($time->rawFormat('Y-m-d'), $training->getNext()->rawFormat('Y-m-d'));
        $training = $this->trainingRepository->upStatusTraining($training);
        $this->assertEquals('third', $training->getStatus());
        $time = Carbon::now(Database::DEFAULT_TZ)->addMinutes(3 * 24 * 60);
        $this->assertEquals($time->rawFormat('Y-m-d'), $training->getNext()->rawFormat('Y-m-d'));
        $training = $this->trainingRepository->upStatusTraining($training);
        $this->assertEquals('fourth', $training->getStatus());
        $time = Carbon::now(Database::DEFAULT_TZ)->addMinutes(7 * 24 * 60);
        $this->assertEquals($time->rawFormat('Y-m-d'), $training->getNext()->rawFormat('Y-m-d'));
        $training = $this->trainingRepository->upStatusTraining($training);
        $this->assertEquals('fifth', $training->getStatus());
        $time = Carbon::now(Database::DEFAULT_TZ)->addMinutes(30 * 24 * 60);
        $this->assertEquals($time->rawFormat('Y-m-d'), $training->getNext()->rawFormat('Y-m-d'));
        $training = $this->trainingRepository->upStatusTraining($training);
        $this->assertEquals('sixth', $training->getStatus());
        $time = Carbon::now(Database::DEFAULT_TZ)->addMinutes(90 * 24 * 60);
        $this->assertEquals($time->rawFormat('Y-m-d'), $training->getNext()->rawFormat('Y-m-d'));
        $training = $this->trainingRepository->upStatusTraining($training);
        $this->assertEquals('never', $training->getStatus());
        $time = Carbon::now(Database::DEFAULT_TZ)->addMinutes(180 * 24 * 60);
        $this->assertEquals($time->rawFormat('Y-m-d'), $training->getNext()->rawFormat('Y-m-d'));
        $training = $this->trainingRepository->upStatusTraining($training);
        $this->assertEquals('never', $training->getStatus());
        $time = Carbon::now(Database::DEFAULT_TZ)->addMinutes(360 * 24 * 60);
        $this->assertEquals($time->rawFormat('Y-m-d'), $training->getNext()->rawFormat('Y-m-d'));
        $this->assertEquals('never', $training->getStatus());
        $time = Carbon::now(Database::DEFAULT_TZ)->addMinutes(360 * 24 * 60);
        $this->assertEquals($time->rawFormat('Y-m-d'), $training->getNext()->rawFormat('Y-m-d'));
    }
}
