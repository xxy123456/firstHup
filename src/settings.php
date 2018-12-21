<?php
return [

    'settings' => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,
        // Monolog settings
        'logger' => [
            'name' => env('LOG_NAME','slim_log'),
            'path' =>  'php://stdout',
            'level' => env('LOG_DEBUG',false) ? \Monolog\Logger::DEBUG : \Monolog\Logger::INFO ,
        ],
        'oauth' => [
            'privateKey'    => __DIR__ . '/OAuth/sslKeys/private.key',
            'publicKey'     => __DIR__ . '/OAuth/sslKeys/public.key',
            'encryptionKey' => 'def000002a48d3200dfbd8abb40497590b610a8b2b4ac98ac6052efc40999608c127d9d9b332d37be11ac49c4cff8e507dc71cc82a9af723760a2f9c67584abb13b06cae',
            'dateTimeZone'  => 'Asia/Shanghai',
        ],
        'dynamoDB' => [
            'region'      => 'cn-north-1',
            'version'     => 'latest',
            'credentials' => [
                'key'    => 'AKIAOQV5XNRGBZW7YGOA',
                'secret' => '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',
            ],
        ],
        'redis' => [
            'scheme'   => 'tcp',
            'host'     => 'redis',
            'port'     => 6379,
            'database' => 15
        ]
    ],



];
