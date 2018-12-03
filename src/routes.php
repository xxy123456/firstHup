<?php

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controllers\OAuthController;
// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
    $this->logger->info("zhaozhikai '/' route");
    echo 'hello,world';
});

$app->get('/index', function (Request $request, Response $response, array $args) {
    echo phpinfo();
});

$app->get('/authorize', OAuthController::class . ':authorize');
$app->post('/access_token', OAuthController::class . ':accessToken');

$app->group('/auth', function () use ($app) {
    $app->get('', OAuthController::class . ':index');
})->add(new \League\OAuth2\Server\Middleware\ResourceServerMiddleware($container->get('oauthResourceServer')));
// not running __construct
//$app->post('/access_token', [OAuthController::class, 'accessToken']);
