<?php

use RepeatBot\Bot\Service\ExportService;
use RepeatBot\Core\App;
use RepeatBot\Core\Bot;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Database\Repository\ExportRepository;
use RepeatBot\Core\Database\Repository\TrainingRepository;
use RepeatBot\Core\Log;
use RepeatBot\Core\Metric;

require __DIR__ . '/vendor/autoload.php';

$app = App::getInstance()->init();
$config = $app->getConfig();
$logger = Log::getInstance()->init($config)->getLogger();
$bot = Bot::getInstance();
$bot->init($config, $logger);
$metric = Metric::getInstance()->init($config);
$database = Database::getInstance()->init($config)->getConnection();
$trainingRepository = new TrainingRepository($database);
$exportRepository = new ExportRepository($database);
$service = new ExportService($trainingRepository, $exportRepository);
$expectedTime = time() + 60; // +1 min in seconds
$oneSecond = time();
while (true) {
    $now = time();
    if ($now >= $oneSecond) {
        $oneSecond = $now + 1;
        try {
            $bot->queue($exportRepository, $service);
            $metric->increaseMetric('queue');
        } catch (\Throwable $e) {
            $logger->error($e->getMessage(), $e->getTrace());
        }
    }
}
