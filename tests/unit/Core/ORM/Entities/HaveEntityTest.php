<?php

declare(strict_types=1);

namespace Tests\Unit\Core\ORM\Entities;

use Carbon\Carbon;
use Codeception\Exception\ModuleException;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Entities\UserNotification;
use RepeatBot\Core\ORM\Entities\UserVoice;
use RepeatBot\Core\ORM\Entities\Version;
use RepeatBot\Core\ORM\Entities\VersionNotification;
use RepeatBot\Core\ORM\Entities\Word;
use UnitTester;

/**
 * Class HaveEntityTest
 * @package Tests\Unit\Core\ORM\Entities
 */
final class HaveEntityTest extends Unit
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
    
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function testHaveVersion(): void
    {
        $used = 1;
        $versionText = '4.0.0';
        $created = Carbon::now();
        $description = 'text';

        $entity = new Version();
        $entity->setUsed($used);
        $entity->setVersion($versionText);
        $entity->setCreatedAt($created);
        $entity->setDescription($description);
        $this->tester->haveVersionInDatabase($entity);

        $version = $this->em->find(Version::class, $entity->getId());

        $this->tester->assertEquals($used, $version->getUsed());
        $this->tester->assertEquals($versionText, $version->getVersion());
        $this->tester->assertEquals($created, $version->getCreatedAt());
        $this->tester->assertEquals($description, $version->getDescription());
    }
    
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function testHaveVersionNotification(): void
    {
        $chatId = 42;
        $versionId = 42;
        $created = Carbon::now();

        $entity = new VersionNotification();
        $entity->setChatId($chatId);
        $entity->setVersionId($versionId);
        $entity->setCreatedAt($created);
        $this->tester->haveVersionNotificationInDatabase($entity);

        $versionNotification = $this->em->find(VersionNotification::class, $entity->getId());

        $this->tester->assertEquals($versionId, $versionNotification->getVersionId());
        $this->tester->assertEquals($chatId, $versionNotification->getChatId());
        $this->tester->assertEquals($created, $versionNotification->getCreatedAt());
    }
    
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function testHaveUserVoice(): void
    {
        $userId = 42;
        $used = 0;
        $voice = 'en-US-Wavenet-A';
        $created = Carbon::now();
        $updated = Carbon::now();

        $entity = new UserVoice();
        $entity->setUserId($userId);
        $entity->setVoice($voice);
        $entity->setUsed($used);
        $entity->setCreatedAt($created);
        $entity->setUpdatedAt($updated);
        $this->tester->haveUserVoiceInDatabase($entity);

        $userVoice = $this->em->find(UserVoice::class, $entity->getId());

        $this->tester->assertEquals($userId, $userVoice->getUserId());
        $this->tester->assertEquals($used, $userVoice->getUsed());
        $this->tester->assertEquals($voice, $userVoice->getVoice());
        $this->tester->assertEquals($created, $userVoice->getCreatedAt());
        $this->tester->assertEquals($updated, $userVoice->getUpdatedAt());
    }
    
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function testHaveUserNotification(): void
    {
        $userId = 42;
        $silent = 0;
        $deleted = 0;
        $deletedAt = Carbon::now();

        $entity = new UserNotification();
        $entity->setUserId($userId);
        $entity->setSilent($silent);
        $entity->setDeleted($deleted);
        $entity->setDeletedAt($deletedAt);
        $this->tester->haveUserNotificationInDatabase($entity);

        $userNotification = $this->em->find(UserNotification::class, $entity->getId());

        $this->tester->assertEquals($userId, $userNotification->getUserId());
        $this->tester->assertEquals($silent, $userNotification->getSilent());
        $this->tester->assertEquals($deleted, $userNotification->getDeleted());
        $this->tester->assertEquals($deletedAt, $userNotification->getDeletedAt());
    }
    
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function testHaveTraining(): void
    {
        $word = new Word();
        $word->setWord('tmp');
        $word->setCollectionId(37);
        $word->setTranslate('tmp');
        $this->tester->haveWordInDatabase($word);
    
        $userId = 42;
        $collectionId = 1;
        $type = 'FromEnglish';
        $status = 'first';
        $next = Carbon::now();
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

        $this->tester->haveTrainingInDatabase($entity);

        $training = $this->em->find(Training::class, $entity->getId());

        $this->tester->assertEquals($userId, $training->getUserId());
        $this->tester->assertEquals($word, $training->getWord());
        $this->tester->assertEquals($collectionId, $training->getCollectionId());
        $this->tester->assertEquals($type, $training->getType());
        $this->tester->assertEquals($status, $training->getStatus());
        $this->tester->assertEquals($next, $training->getNext());
        $this->tester->assertEquals($created, $training->getCreatedAt());
        $this->tester->assertEquals($updated, $training->getUpdatedAt());
    }
}
