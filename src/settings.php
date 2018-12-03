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
    ],



];
