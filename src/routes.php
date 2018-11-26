<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
    $this->logger->info("zhaozhikai '/' route");
    echo 'hello,world';
});

$app->get('/index', function (Request $request, Response $response, array $args) {
    echo phpinfo();
});
