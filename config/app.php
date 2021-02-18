<?php
declare(strict_types = 1);

return [
    'database' => [
        'host' => getenv('DB_HOST') ?? 'mysql',
        'name' => getenv('DB_NAME') ?? 'test_db',
        'user' => getenv('DB_USERNAME') ?? 'root',
        'password' => getenv('DB_PASSWORD') ?? 'password'
    ],
    'logger' => [
        'name' => '',
        'path' =>  getenv('LOG_PATH'),
        'filename' => 'bot.log',
        'level' => '100',
    ],
    'telegram' => [
        'token' => getenv('TG_API'),
        'bot_name' => getenv('TG_NAME'),
        'admin_id' => getenv('TG_ADMIN_ID'),
        'command_path' => '/app/app/Bot/Commands',
    ],
];