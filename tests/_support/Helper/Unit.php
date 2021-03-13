<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use RepeatBot\Core\ORM\Entities\Collection;
use RepeatBot\Core\ORM\Entities\Export;
use RepeatBot\Core\ORM\Entities\LearnNotification;
use RepeatBot\Core\ORM\Entities\LearnNotificationPersonal;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Entities\UserNotification;
use RepeatBot\Core\ORM\Entities\UserVoice;
use RepeatBot\Core\ORM\Entities\Version;
use RepeatBot\Core\ORM\Entities\VersionNotification;

class Unit extends \Codeception\Module
{
    public function haveVersionInDatabase(Version $entity): Version
    {
        return HaveInDatabase::getInstance()->haveVersionEntity($entity);
    }

    public function haveVersionNotificationInDatabase(VersionNotification $entity): VersionNotification
    {
        return HaveInDatabase::getInstance()->haveVersionNotificationEntity($entity);
    }
    
    public function haveUserVoiceEntity(UserVoice $entity): UserVoice
    {
        return HaveInDatabase::getInstance()->haveUserVoiceEntity($entity);
    }
    
    public function haveUserNotificationEntity(UserNotification $entity): UserNotification
    {
        return HaveInDatabase::getInstance()->haveUserNotificationEntity($entity);
    }
    
    public function haveTrainingEntity(Training $entity): Training
    {
        return HaveInDatabase::getInstance()->haveTrainingEntity($entity);
    }
    
    public function haveLearnNotificationPersonalEntity(LearnNotificationPersonal $entity): LearnNotificationPersonal
    {
        return HaveInDatabase::getInstance()->haveLearnNotificationPersonalEntity($entity);
    }
    
    public function haveLearnNotificationEntity(LearnNotification $entity): LearnNotification
    {
        return HaveInDatabase::getInstance()->haveLearnNotificationEntity($entity);
    }
    
    public function haveExportEntity(Export $entity): Export
    {
        return HaveInDatabase::getInstance()->haveExportEntity($entity);
    }
    
    public function haveCollectionEntity(Collection $entity): Collection
    {
        return HaveInDatabase::getInstance()->haveCollectionEntity($entity);
    }
}
