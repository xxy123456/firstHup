<?php

$container = $app->getContainer();

// monolog
$container['logger'] = function ($container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
$container['aa'] = function ($container) {
    return 116;
};
// OAuth2 authorization server
$container['oauth'] = function ($container) {
    $clientRepository = new \App\OAuth\Repositories\ClientRepository($container); // instance of ClientRepositoryInterface
    $scopeRepository = new \App\OAuth\Repositories\ScopeRepository($container); // instance of ScopeRepositoryInterface
    $accessTokenRepository = new \App\OAuth\Repositories\AccessTokenRepository($container); // instance of AccessTokenRepositoryInterface
    $userRepository = new \App\OAuth\Repositories\UserRepository($container); // instance of UserRepositoryInterface
    $refreshTokenRepository = new \App\OAuth\Repositories\RefreshTokenRepository($container); // instance of RefreshTokenRepositoryInterface
    $authCodeRepository = new \App\OAuth\Repositories\AuthCodeRepository($container); // instance of AuthCodeRepositoryInterface

    $settings = $container->get('settings')['oauth'];

    $server = new \League\OAuth2\Server\AuthorizationServer(
        $clientRepository,
        $accessTokenRepository,
        $scopeRepository,
        $settings['privateKey'],
        \Defuse\Crypto\Key::loadFromAsciiSafeString($settings['encryptionKey'])
    );

    // Password grant
    $passwordGrant = new \League\OAuth2\Server\Grant\PasswordGrant(
        $userRepository,
        $refreshTokenRepository
    );
    $passwordGrant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month
    $server->enableGrantType(
        $passwordGrant,
        new \DateInterval('PT1H') // access tokens will expire after 1 hour
    );

    // ClientCredentialsGrant
    $server->enableGrantType(
        new \League\OAuth2\Server\Grant\ClientCredentialsGrant(),
        new \DateInterval('PT1H')
    );

    // AuthCodeGrant
    $AuthCodeGrant = new \League\OAuth2\Server\Grant\AuthCodeGrant(
        $authCodeRepository,
        $refreshTokenRepository,
        new \DateInterval('PT10M') // authorization codes will expire after 10 minutes
    );
    $AuthCodeGrant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month
    $server->enableGrantType(
        $AuthCodeGrant,
        new \DateInterval('PT1H')
    );

    // ImplicitGrant
    $server->enableGrantType(
        new \League\OAuth2\Server\Grant\ImplicitGrant(new \DateInterval('PT1H')),
        new \DateInterval('PT1H')
    );

    // RefreshToken
    $grant = new \League\OAuth2\Server\Grant\RefreshTokenGrant($refreshTokenRepository);
    $grant->setRefreshTokenTTL(new \DateInterval('P1M'));
    $server->enableGrantType(
        $grant,
        new \DateInterval('PT1H')
    );

    return $server;
};

// OAuth2 resource server
$container['oauthResourceServer'] = function ($container) {
    $accessTokenRepository = new \App\OAuth\Repositories\AccessTokenRepository($container);
    $settings = $container->get('settings')['oauth'];

    return new \League\OAuth2\Server\ResourceServer(
        $accessTokenRepository,
        $settings['publicKey']
    );
};

// aws dynamoDB sdk
$container['dynamoDB'] = function ($container) {
    $setting = $container->get('settings')['dynamoDB'];
    return new \Aws\DynamoDb\DynamoDbClient([
        'region' => $setting['region'],
        'version' => $setting['version'],
        'credentials' => [
            'key' => $setting['credentials']['key'],
            'secret' => $setting['credentials']['secret']
        ]
    ]);
};

$container['predis'] = function ($container) {
    $settings = $container->get('settings')['redis'];
    return new \Predis\Client($settings);
};

$container['cache'] = function ($container) {
    return new \Symfony\Component\Cache\Simple\RedisCache($container->get('predis'));
};
