<?php

use RepeatBot\Bot\Service\ExportQueueService;
use RepeatBot\Core\App;
use RepeatBot\Core\Bot;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Log;
use RepeatBot\Core\Metric;

require __DIR__ . '/vendor/autoload.php';

$app = App::getInstance()->init();
$config = $app->getConfig();
$logger = Log::getInstance()->init($config)->getLogger();
$bot = Bot::getInstance();
$bot->init($config, $logger);
$metric = Metric::getInstance()->init($config);
$trainingRepository =  Database::getInstance()->init($config)
    ->getEntityManager()
    ->getRepository(\RepeatBot\Core\ORM\Entities\Training::class);
$exportRepository = Database::getInstance()
    ->getEntityManager()
    ->getRepository(\RepeatBot\Core\ORM\Entities\Export::class);
$service = new ExportQueueService($trainingRepository, $exportRepository);
while (true) {
    try {
        $bot->queue($exportRepository, $service);
        $metric->increaseMetric('queue');
        usleep(500000);
    } catch (\Throwable $e) {
        $logger->error($e->getMessage(), $e->getTrace());
    }
}
