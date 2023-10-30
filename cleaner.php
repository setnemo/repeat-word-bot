<?php

use RepeatBot\Core\App;
use RepeatBot\Core\Bot;
use RepeatBot\Core\Database;
use RepeatBot\Core\Log;
use RepeatBot\Core\ORM\Entities\LearnNotification;

require __DIR__ . '/vendor/autoload.php';

$app    = App::getInstance()->init();
$config = $app->getConfig();
$logger = Log::getInstance()->init($config)->getLogger();
$bot    = Bot::getInstance();
$bot->init($config, $logger);
Database::getInstance()->init($config)
    ->getEntityManager()
    ->getRepository(LearnNotification::class)
    ->deleteDeprecatedNotifications();

