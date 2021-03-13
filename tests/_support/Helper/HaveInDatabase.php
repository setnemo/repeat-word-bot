<?php

declare(strict_types=1);

namespace Helper;

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

class HaveInDatabase extends ORM
{
    public function haveWordEntity(Word $entity): Word
    {
        return $this->updateEntity($entity);
    }

    public function haveVersionEntity(Version $entity): Version
    {
        return $this->updateEntity($entity);
    }

    public function haveVersionNotificationEntity(VersionNotification $entity): VersionNotification
    {
        return $this->updateEntity($entity);
    }

    public function haveUserVoiceEntity(UserVoice $entity): UserVoice
    {
        return $this->updateEntity($entity);
    }

    public function haveUserNotificationEntity(UserNotification $entity): UserNotification
    {
        return $this->updateEntity($entity);
    }

    public function haveTrainingEntity(Training $entity): Training
    {
        return $this->updateEntity($entity);
    }

    public function haveLearnNotificationPersonalEntity(LearnNotificationPersonal $entity): LearnNotificationPersonal
    {
        return $this->updateEntity($entity);
    }

    public function haveLearnNotificationEntity(LearnNotification $entity): LearnNotification
    {
        return $this->updateEntity($entity);
    }

    public function haveExportEntity(Export $entity): Export
    {
        return $this->updateEntity($entity);
    }

    public function haveCollectionEntity(Collection $entity): Collection
    {
        return $this->updateEntity($entity);
    }

    /**
     * @param object $entity
     *
     * @return object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function updateEntity(object $entity): object
    {
        $em = self::getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }
}
