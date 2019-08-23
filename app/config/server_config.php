<?php

use Phalcon\Config;

return new \Phalcon\Config([
    'database' => [
        'db' => [
            'host' => '127.0.0.1',
            'username' => 'root',
            'password' => '123456',
            'dbname' => 'test',
            'charset' => 'utf8'
        ]
    ],
    'redis' => [
        'cache' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'timeout' => 2
        ],
        'user' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'timeout' => 2,
            'index' => 1
        ]
    ]
]);
