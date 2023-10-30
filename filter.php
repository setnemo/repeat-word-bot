<?php

use RepeatBot\Core\App;
use RepeatBot\Core\Bot;
use RepeatBot\Core\Database;
use RepeatBot\Core\Log;
use RepeatBot\Core\Metric;

require __DIR__ . '/vendor/autoload.php';

$app    = App::getInstance()->init();
$config = $app->getConfig();
$logger = Log::getInstance()->init($config)->getLogger();
$bot    = Bot::getInstance();
$bot->init($config, $logger);
$metric         = Metric::getInstance()->init($config);
$wordRepository = Database::getInstance()->init($config)
    ->getEntityManager()
    ->getRepository(\RepeatBot\Core\ORM\Entities\Word::class);

$items = $wordRepository->getWordsForTranslate($argv[1] ?? 0);
foreach ($items as $item) {
    $translate = $item->getTranslate();
    echo $item->getId() . ':' . $translate . "\n";
    $words = array_unique(explode('; ', $translate));
    $translate = implode('; ', $words);
    echo $item->getId() . ':' . $translate . "\n";
    $wordRepository->updateWord($item->getId(), $translate);
}


