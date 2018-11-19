<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Controllers\TestController;
// Routes

$app->get('/', function (Request $request, Response $response, array $args) {

    $this->logger->info("zhaozhikai '/' route");
    echo 'hello,world';

});
