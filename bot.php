<?php

use RepeatBot\Core\App;
use RepeatBot\Core\Bot;
use RepeatBot\Core\Log;

require __DIR__ . '/vendor/autoload.php';

$app = App::getInstance()->init();
$config = $app->getConfig();
$logger = Log::getInstance()->init($config)->getLogger();
$bot = Bot::getInstance();
$bot->init($config, $logger);
$expectedTime = time() + 10 * 60;
while (true) {
    $bot->run();
    if ($expectedTime <= time()) {
        die(0);
    }
}

