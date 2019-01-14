<?php

use App\Controllers\TestController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controllers\OAuthController;
// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
//	$a=$this->get('settings')['logger'];
//    var_dump($a);
    echo 132;

});

$app->get('/index', function (Request $request, Response $response, array $args) {
	//var_dump($args);
    echo phpinfo();
});

$app->get('/test',TestController::class . ':index');


$app->get('/authorize', OAuthController::class . ':authorize');
$app->post('/access_token', OAuthController::class . ':accessToken');
$app->group('/oauth', function () use ($app) {
    $app->get('/a', OAuthController::class . ':index');
})->add(new \League\OAuth2\Server\Middleware\ResourceServerMiddleware($container->get('oauthResourceServer')));

$app->group('/auth', function () use ($app) {
    $app->get('/b', OAuthController::class . ':server');
});
// not running __construct
//$app->post('/access_token', [OAuthController::class, 'accessToken']);
