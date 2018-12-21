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
}
