<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use RepeatBot\Core\App;
use RepeatBot\Core\Database;

require __DIR__ . '/vendor/autoload.php';

$app = App::getInstance()->init();
$config = $app->getConfig();
$database = Database::getInstance()->init($config);
$entityManager = $database->getEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
