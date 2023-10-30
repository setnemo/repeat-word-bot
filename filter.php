<?php

use RepeatBot\Core\App;
use RepeatBot\Core\Bot;
use RepeatBot\Core\Database;
use RepeatBot\Core\Log;
use RepeatBot\Core\Metric;
use RepeatBot\Core\ORM\Entities\Word;

require __DIR__ . '/vendor/autoload.php';

$app    = App::getInstance()->init();
$config = $app->getConfig();
$logger = Log::getInstance()->init($config)->getLogger();
$bot    = Bot::getInstance();
$bot->init($config, $logger);
$metric         = Metric::getInstance()->init($config);
$wordRepository = Database::getInstance()->init($config)
    ->getEntityManager()
    ->getRepository(Word::class);

$items = $wordRepository->getWordsForTranslate($argv[1] ?? 0);


$subscription_key = $config->getKey('translate_key_azure');

$path = "https://api.cognitive.microsofttranslator.com/dictionary/lookup?api-version=3.0&from=en&to=uk";

$text = "great";

if (!function_exists('com_create_guid')) {
    function com_create_guid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}

function dictionaryLookup($path, $key, $content): string
{
    return json_encode(
        json_decode(
            file_get_contents(
                $path,
                false,
                stream_context_create([
                    'http' => [
                        'header'  => "Content-type: application/json\r\n" .
                            "Content-length: " . strlen($content) . "\r\n" .
                            "Ocp-Apim-Subscription-Key: $key\r\n" .
                            "X-ClientTraceId: " . com_create_guid() . "\r\n",
                        'method'  => 'POST',
                        'content' => $content,
                    ],
                ])
            )
        ),
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );
}

foreach ($items as $item) {
    $res = dictionaryLookup($path, $subscription_key, json_encode([
        [
            'Text' => $item->getWord(),
        ],
    ]));
    var_dump($res);
    echo $item->getId() . ':'  . "\n";
//    $wordRepository->updateWord($item->getId(), $translate);
}


