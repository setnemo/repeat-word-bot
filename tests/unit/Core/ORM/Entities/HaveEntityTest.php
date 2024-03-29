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
use RepeatBot\Core\ORM\Entities\Collection;
use RepeatBot\Core\ORM\Entities\Export;
use RepeatBot\Core\ORM\Entities\LearnNotification;
use RepeatBot\Core\ORM\Entities\LearnNotificationPersonal;
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
    protected function _setUp(): void
    {
        $this->em = $this->getModule('Doctrine2')->em;
        parent::_setUp();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function testHaveWord(): void
    {
        $word = 'wordddd';
        $translate = 'translateeee';
        $collectionId = 37;
        $created = Carbon::now();

        $entity = new Word();
        $entity->setWord($word);
        $entity->setCollectionId($collectionId);
        $entity->setTranslate($translate);
        $entity->setCreatedAt($created);

        $this->tester->haveWordEntity($entity);

        $version = $this->em->find(Word::class, $entity->getId());

        $this->tester->assertEquals($word, $version->getWord());
        $this->tester->assertEquals($translate, $version->getTranslate());
        $this->tester->assertEquals($collectionId, $version->getCollectionId());
        $this->tester->assertEquals($created, $version->getCreatedAt());
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
        $this->tester->haveVersionEntity($entity);

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
        $this->tester->haveVersionNotificationEntity($entity);

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
        $this->tester->haveUserVoiceEntity($entity);

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
        $this->tester->haveUserNotificationEntity($entity);

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
        $this->tester->haveWordEntity($word);

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

        $this->tester->haveTrainingEntity($entity);

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

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function testHaveLearnNotificationPersonal(): void
    {
        $userId = 42;
        $alarm = Carbon::now();
        $message = 'message';
        $tz = 'FDT';
        $created = Carbon::now();
        $updated = Carbon::now();

        $entity = new LearnNotificationPersonal();
        $entity->setUserId($userId);
        $entity->setMessage($message);
        $entity->setTimezone($tz);
        $entity->setAlarm($alarm);
        $entity->setCreatedAt($created);
        $entity->setUpdatedAt($updated);

        $this->tester->haveLearnNotificationPersonalEntity($entity);

        $training = $this->em->find(LearnNotificationPersonal::class, $entity->getId());

        $this->tester->assertEquals($userId, $training->getUserId());
        $this->tester->assertEquals($alarm, $training->getAlarm());
        $this->tester->assertEquals($message, $training->getMessage());
        $this->tester->assertEquals($tz, $training->getTimezone());
        $this->tester->assertEquals($created, $training->getCreatedAt());
        $this->tester->assertEquals($updated, $training->getUpdatedAt());
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function testHaveLearnNotification(): void
    {
        $userId = 42;
        $message = 'message';
        $used = 0;
        $silent = 0;
        $created = Carbon::now();
        $updated = Carbon::now();

        $entity = new LearnNotification();
        $entity->setUserId($userId);
        $entity->setMessage($message);
        $entity->setUsed($used);
        $entity->setSilent($silent);
        $entity->setCreatedAt($created);
        $entity->setUpdatedAt($updated);

        $this->tester->haveLearnNotificationEntity($entity);

        $learnNotification = $this->em->find(LearnNotification::class, $entity->getId());

        $this->tester->assertEquals($userId, $learnNotification->getUserId());
        $this->tester->assertEquals($message, $learnNotification->getMessage());
        $this->tester->assertEquals($silent, $learnNotification->getSilent());
        $this->tester->assertEquals($used, $learnNotification->getUsed());
        $this->tester->assertEquals($created, $learnNotification->getCreatedAt());
        $this->tester->assertEquals($updated, $learnNotification->getUpdatedAt());
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function testHaveExport(): void
    {
        $userId = 42;
        $chatId = 42;
        $wordType = 'FromEnglish_first';
        $used = 0;
        $created = Carbon::now();
        $updated = Carbon::now();

        $entity = new Export();
        $entity->setUserId($userId);
        $entity->setChatId($chatId);
        $entity->setWordType($wordType);
        $entity->setUsed($used);
        $entity->setCreatedAt($created);
        $entity->setUpdatedAt($updated);

        $this->tester->haveExportEntity($entity);

        $export = $this->em->find(Export::class, $entity->getId());

        $this->tester->assertEquals($userId, $export->getUserId());
        $this->tester->assertEquals($chatId, $export->getChatId());
        $this->tester->assertEquals($wordType, $export->getWordType());
        $this->tester->assertEquals($used, $export->getUsed());
        $this->tester->assertEquals($created, $export->getCreatedAt());
        $this->tester->assertEquals($updated, $export->getUpdatedAt());
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function testHaveCollection(): void
    {
        $name = 'Collection';
        $created = Carbon::now();

        $entity = new Collection();
        $entity->setName($name);
        $entity->setCreatedAt($created);

        $this->tester->haveCollectionEntity($entity);

        $export = $this->em->find(Collection::class, $entity->getId());

        $this->tester->assertEquals($name, $export->getName());
        $this->tester->assertEquals($created, $export->getCreatedAt());
    }
}
