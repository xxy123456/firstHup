<?php

$container = $app->getContainer();


// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};


$container['config'] = function ($c) {
    $configCache = [];
    return function ($filename) use ($configCache) {
        if (!isset($configCache[$filename])) {
            $path = "config/{$filename}.php" ;
            $configCache[$filename] = require $path;
        }
        return $configCache[$filename];
    };
};