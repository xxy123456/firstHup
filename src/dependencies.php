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

// OAuth2
$container['oauth'] = function ($c) {
    $clientRepository = new \App\OAuth\Repositories\ClientRepository(); // instance of ClientRepositoryInterface
    $scopeRepository = new \App\OAuth\Repositories\ScopeRepository(); // instance of ScopeRepositoryInterface
    $accessTokenRepository = new \App\OAuth\Repositories\AccessTokenRepository(); // instance of AccessTokenRepositoryInterface
    $userRepository = new \App\OAuth\Repositories\UserRepository(); // instance of UserRepositoryInterface
    $refreshTokenRepository = new \App\OAuth\Repositories\RefreshTokenRepository(); // instance of RefreshTokenRepositoryInterface
    $authCodeRepository = new \App\OAuth\Repositories\AuthCodeRepository(); // instance of AuthCodeRepositoryInterface

    $settings = $c->get('settings')['oauth'];

    $server = new \League\OAuth2\Server\AuthorizationServer(
        $clientRepository,
        $accessTokenRepository,
        $scopeRepository,
        $settings['privateKey'],
        $settings['encryptionKey']
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

    return $server;
};
