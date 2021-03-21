<?php

use RepeatBot\Core\App;
use RepeatBot\Core\Bot;
use RepeatBot\Core\Cache;
use RepeatBot\Core\Database;
use RepeatBot\Core\Log;
use RepeatBot\Core\Metric;

require __DIR__ . '/vendor/autoload.php';

$app = App::getInstance()->init();
$config = $app->getConfig();
Database::getInstance()->init($config);
$logger = Log::getInstance()->init($config)->getLogger();
$bot = Bot::getInstance();
$bot->init($config, $logger);
$metric = Metric::getInstance()->init($config);
Cache::getInstance()->init($config);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bot->runHook($config);
} else {
    echo json_encode(
        ["repeat-bot" => "ok"]
    );
}
