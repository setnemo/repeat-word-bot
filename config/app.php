<?php

declare(strict_types=1);

return [
    'database' => [
        'host' => getenv('DB_HOST') ?? 'mysql',
        'name' => getenv('DB_NAME') ?? 'test_db',
        'user' => getenv('DB_USERNAME') ?? 'root',
        'dev_mode' => getenv('DB_DEV_MODE') ?? '0',
        'password' => getenv('DB_PASSWORD') ?? 'password',
        'entity_path' => getenv('DB_ENTITY_PATH') ?? '/app/app/Core/ORM/Entities/'
    ],
    'logger' => [
        'name' => '',
        'path' =>  getenv('LOG_PATH') ?? '/app/logs/app/',
        'filename' => 'bot.log',
        'level' => '400',
    ],
    'telegram' => [
        'token' => getenv('TG_API'),
        'bot_name' => getenv('TG_NAME'),
        'admin_id' => getenv('TG_ADMIN_ID'),
        'command_path' => '/app/app/Bot/Commands',
        'hook_host' => (string) getenv('HOOK_HOST'),
    ],
    'redis' => [
        'host' => getenv('REDIS_HOST'),
        'port' => (string) getenv('REDIS_PORT'),
        'database' => getenv('REDIS_DB'),
        'database2' => '2',
    ],
];
