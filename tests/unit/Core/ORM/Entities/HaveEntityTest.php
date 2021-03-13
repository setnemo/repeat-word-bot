<?php

declare(strict_types=1);

namespace Tests\Unit\Core\ORM\Entities;

use Carbon\Carbon;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManager;
use RepeatBot\Core\ORM\Entities\UserVoice;
use RepeatBot\Core\ORM\Entities\Version;
use RepeatBot\Core\ORM\Entities\VersionNotification;
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
     * @throws \Codeception\Exception\ModuleException
     */
    protected function _setUp()
    {
        $this->em = $this->getModule('Doctrine2')->em;
        parent::_setUp();
    }
    
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function testHaveVersion(): void
    {
        $used = 1;
        $version = '4.0.0';
        $created = Carbon::now();
        $description = 'text';

        $entity = new Version();
        $entity->setUsed($used);
        $entity->setVersion($version);
        $entity->setCreatedAt($created);
        $entity->setDescription($description);
        $this->tester->haveVersionInDatabase($entity);

        $dbEntity = $this->em->find(Version::class, $entity->getId());

        $this->tester->assertEquals($used, $dbEntity->getUsed());
        $this->tester->assertEquals($version, $dbEntity->getVersion());
        $this->tester->assertEquals($created, $dbEntity->getCreatedAt());
        $this->tester->assertEquals($description, $dbEntity->getDescription());
    }
    
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
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

        $version = $this->em->find(VersionNotification::class, $entity->getId());

        $this->tester->assertEquals($versionId, $version->getVersionId());
        $this->tester->assertEquals($chatId, $version->getChatId());
        $this->tester->assertEquals($created, $version->getCreatedAt());
    }
    
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function testHaveUserVoiceNotification(): void
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
        $this->tester->haveVersionNotificationInDatabase($entity);

        $version = $this->em->find(UserVoice::class, $entity->getId());

        $this->tester->assertEquals($userId, $version->getUserId());
        $this->tester->assertEquals($used, $version->getUsed());
        $this->tester->assertEquals($voice, $version->getVoice());
        $this->tester->assertEquals($created, $version->getCreatedAt());
        $this->tester->assertEquals($updated, $version->getUpdatedAt());
    }
}
