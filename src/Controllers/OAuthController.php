<?php
/**
 * Created by PhpStorm.
 * User: zhaozhikai
 * Date: 2018/11/6
 * Time: 下午4:27
 */

namespace App\Controllers;

use App\OAuth\Entities\UserEntity;
use League\OAuth2\Server\Exception\OAuthServerException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Cache\Simple\RedisCache;

class OAuthController
{
    public $ci;
    public function __construct(Container $ci)
    {
        $this->ci = $ci;
    }

    public function index(Request $request, Response $response)
    {
        echo 899;
    }

    public function authorize(Request $request, Response $response)
    {
        /* @var \League\OAuth2\Server\AuthorizationServer $server */
        $server = $this->ci->get('oauth');
        // Validate the HTTP request and return an AuthorizationRequest object.
        $authRequest = $server->validateAuthorizationRequest($request);

        // The auth request object can be serialized and saved into a user's session.
        // You will probably want to redirect the user at this point to a login endpoint.

        // Once the user has logged in set the user on the AuthorizationRequest
        $authRequest->setUser(new UserEntity()); // an instance of UserEntityInterface

        // At this point you should redirect the user to an authorization page.
        // This form will ask the user to approve the client and the scopes requested.

        // Once the user has approved or denied the client update the status
        // (true = approved, false = denied)
        $authRequest->setAuthorizationApproved(true);

        // Return the HTTP redirect response
        return $server->completeAuthorizationRequest($authRequest, $response);
    }

    public function accessToken(Request $request, Response $response)
    {

        /* @var \League\OAuth2\Server\AuthorizationServer $server */
        $server = $this->ci->get('oauth');
        // Try to respond to the request
        return $server->respondToAccessTokenRequest($request, $response);
    }

    public function server(){
        // Init our repositories
        $clientRepository = new \App\OAuth\Repositories\ClientRepository($this->ci); // instance of ClientRepositoryInterface
        $scopeRepository = new \App\OAuth\Repositories\ScopeRepository($this->ci); // instance of ScopeRepositoryInterface
        $accessTokenRepository = new \App\OAuth\Repositories\AccessTokenRepository($this->ci); // instance of AccessTokenRepositoryInterface
        $userRepository = new \App\OAuth\Repositories\UserRepository($this->ci); // instance of UserRepositoryInterface
        $refreshTokenRepository = new \App\OAuth\Repositories\RefreshTokenRepository($this->ci); // instance of RefreshTokenRepositoryInterface

        // Path to public and private keys
        $privateKey =   __DIR__."/private.key";
        //$privateKey = new CryptKey('file://path/to/private.key', 'passphrase'); // if private key has a pass phrase
        $encryptionKey = 'lxZFUEsBCJ2Yb14IF2ygAHI5N4+ZAUXXaSeeJm6+twsUmIen'; // generate using base64_encode(random_bytes(32))

        // Setup the authorization server
        $server = new \League\OAuth2\Server\AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            $encryptionKey
        );
        $grant = new \League\OAuth2\Server\Grant\PasswordGrant(
             $userRepository,
             $refreshTokenRepository
        );

        $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month

        // Enable the password grant on the server
        $server->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );

    }
    public function client(){
        
    }
}
