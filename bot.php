<?php

use RepeatBot\Core\App;
use RepeatBot\Core\Bot;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Log;
use RepeatBot\Core\Metric;

require __DIR__ . '/vendor/autoload.php';

$app = App::getInstance()->init();
$config = $app->getConfig();
$logger = Log::getInstance()->init($config)->getLogger();
Database::getInstance()->init($config);
$bot = Bot::getInstance();
$bot->init($config, $logger);

$metric = Metric::getInstance()->init($config);

$bot->botBefore();
while (true) {
    $bot->run();
    $metric->increaseMetric('worker');
}

