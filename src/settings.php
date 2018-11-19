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
    ],
];
