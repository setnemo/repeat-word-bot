<?php

use RepeatBot\Bot\Service\ExportService;
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
$database = Database::getInstance()->init($config)->getConnection();
$trainingRepository =  Database::getInstance()
    ->getEntityManager()
    ->getRepository(\RepeatBot\Core\ORM\Entities\Training::class);
$exportRepository = Database::getInstance()
    ->getEntityManager()
    ->getRepository(\RepeatBot\Core\ORM\Entities\Export::class);
$service = new ExportService($trainingRepository, $exportRepository);
while (true) {
    try {
        $bot->queue($exportRepository, $service);
        $metric->increaseMetric('queue');
        sleep(1);
    } catch (\Throwable $e) {
        $logger->error($e->getMessage(), $e->getTrace());
    }
}
