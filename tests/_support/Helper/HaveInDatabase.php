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

class HaveInDatabase extends ORM
{
    public function haveVersionEntity(Version $entity): Version
    {
        $em = self::getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    public function haveVersionNotificationEntity(VersionNotification $entity): VersionNotification
    {
        $em = self::getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    public function haveUserVoiceEntity(UserVoice $entity): UserVoice
    {
        $em = self::getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    public function haveUserNotificationEntity(UserNotification $entity): UserNotification
    {
        $em = self::getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    public function haveTrainingEntity(Training $entity): Training
    {
        $em = self::getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    public function haveLearnNotificationPersonalEntity(LearnNotificationPersonal $entity): LearnNotificationPersonal
    {
        $em = self::getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    public function haveLearnNotificationEntity(LearnNotification $entity): LearnNotification
    {
        $em = self::getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    public function haveExportEntity(Export $entity): Export
    {
        $em = self::getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    public function haveCollectionEntity(Collection $entity): Collection
    {
        $em = self::getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }
}
