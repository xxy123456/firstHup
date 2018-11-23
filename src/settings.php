<?php
return [
    'settings' => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' =>  'php://stdout',
//            'path' =>  __DIR__ . '/../logs/logs.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'oauth' => [
            'privateKey'    => __DIR__ . '/OAuth/sslKeys/private.key',
            'encryptionKey' => 'def000002a48d3200dfbd8abb40497590b610a8b2b4ac98ac6052efc40999608c127d9d9b332d37be11ac49c4cff8e507dc71cc82a9af723760a2f9c67584abb13b06cae'
        ]
    ],
];
