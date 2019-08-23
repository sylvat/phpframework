<?php

use Phalcon\Config;
use Phalcon\Logger;

/**
 * @Desc 全局配置文件
 * @Author tangqin
 * @Date 2016/7/26
 * @Time 19:50
 */
return new \Phalcon\Config([
    'application' => [
        'debug' => false,
        'coreDir' => BASE_DIR . '/app/core/',
        'controllersDir' => BASE_DIR . '/app/controllers/',
        'modelsDir' => BASE_DIR . '/app/models/',
        'servicesDir' => BASE_DIR . '/app/services/',
        'cacheDir' => BASE_DIR . '/app/cache/',
        'libsDir' => BASE_DIR . '/app/libs/',
        'logsDir' => BASE_DIR . '/storage/logs/',
        'viewsDir' => BASE_DIR . '/app/views/',
        'redisPrefix' => 'app:',
        'dbPrefix' => 'app_'
    ],
    'volt' => [
        'compiledExt' => '.php',
        'separator' => '_',
        'cacheDir' => BASE_DIR . '/storage/cache/volt/',
        'forceCompile' => true,
    ],
    'session' => [
        'adapter' => '\Phalcon\Session\Adapter\Files',
        'options' => [
            'lifetime' => 600,
            'uniqueId' => ''
        ]
    ],
    'logger' => [
        'path' => BASE_DIR . '/storage/logs/',
        'format' => '[%date%] [%type%] %message%',
        'date' => 'Y-m-d H:i:s',
        'logLevel' => Logger::INFO,
    ],
    'error' => [
        'logger' => BASE_DIR . '/storage/logs/error.log',
        'formatter' => [
            'format' => '[%date%] [%type%] %message%',
            'date' => 'Y-m-d H:i:s',
        ],
        'controller' => 'error',
        'action' => 'show503',
    ]
]);
