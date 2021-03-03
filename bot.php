<?php

use RepeatBot\Core\App;
use RepeatBot\Core\Bot;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\Log;
use RepeatBot\Core\Metric;
use RepeatBot\Core\ORM\Entities\Version;

require __DIR__ . '/vendor/autoload.php';

$app = App::getInstance()->init();
$config = $app->getConfig();
$logger = Log::getInstance()->init($config)->getLogger();
$bot = Bot::getInstance();
$bot->init($config, $logger);
$entityManager = Database::getInstance()->init($config)->getEntityManager();

var_dump(new \RepeatBot\Core\ORM\Entities\Version());

$productRepository = $entityManager->getRepository(Version::class);

$products = $productRepository->findAll();

foreach ($products as $product) {
    echo sprintf("-%s\n", $product->getName());
}

//$metric = Metric::getInstance()->init($config);
//$bot->botBefore();
//while (true) {
//    $bot->run();
//    $metric->increaseMetric('worker');
//}

