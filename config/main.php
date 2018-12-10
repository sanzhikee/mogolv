<?php
/**
 * Created by PhpStorm.
 * User: sanzhar
 * Date: 06.12.18
 * Time: 10:09
 */
(new \Dotenv\Dotenv(__DIR__ . "/../"))->load();

return [
    'db' => [
        'host' => getenv('mysql_host'),
        'username' => getenv('mysql_username'),
        'password' => getenv('mysql_password'),
        'databaseName' => getenv('mysql_database')
    ],
    'queue' => [
        'host' => getenv('queue_host'),
        'port' => getenv('queue_port')
    ],
    'redis' => [
        'host' => getenv('redis_host'),
        'port' => getenv('redis_port'),
        'scheme' => getenv('redis_scheme'),
    ]
];