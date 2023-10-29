<?php

use Google\Cloud\Translate\V2\TranslateClient;
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

$translate = new TranslateClient(['key' => $config->getKey('translate_key')]);


$items = $wordRepository->getWordsForTranslate($args[1] ?? 0);
foreach ($items as $item) {
    $result = $translate->translate($item->getTranslate(), [
        'target' => 'uk'
    ]);
    echo $item->getId() . ':' . $result['text'] . "\n";
    sleep(1);
    $wordRepository->updateWord($item->getId(), $result['text']);
}


