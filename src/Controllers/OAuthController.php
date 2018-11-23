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
        try {

            // Validate the HTTP request and return an AuthorizationRequest object.
            $authRequest = $server->validateAuthorizationRequest($request);

            // The auth request object can be serialized and saved into a user's session.
            // You will probably want to redirect the user at this point to a login endpoint.

            // Once the user has logged in set the user on the AuthorizationRequest
            $authRequest->setUser(new UserEntity(1)); // an instance of UserEntityInterface

            // At this point you should redirect the user to an authorization page.
            // This form will ask the user to approve the client and the scopes requested.

            // Once the user has approved or denied the client update the status
            // (true = approved, false = denied)
            $authRequest->setAuthorizationApproved(true);

            // Return the HTTP redirect response
            return $server->completeAuthorizationRequest($authRequest, $response);

        } catch (OAuthServerException $exception) {

            // All instances of OAuthServerException can be formatted into a HTTP response
            return $exception->generateHttpResponse($response);

        } catch (\Exception $exception) {

            // Unknown exception
            $body = new Stream(fopen('php://temp', 'r+'));
            $body->write($exception->getMessage());
            return $response->withStatus(500)->withBody($body);

        }
    }

    public function accessToken(Request $request, Response $response)
    {
        /* @var \League\OAuth2\Server\AuthorizationServer $server */
        $server = $this->ci->get('oauth');

        try {
            // Try to respond to the request
            return $server->respondToAccessTokenRequest($request, $response);

        } catch (OAuthServerException $exception) {

            // All instances of OAuthServerException can be formatted into a HTTP response
            return $exception->generateHttpResponse($response);

        } catch (\Exception $exception) {

            // Unknown exception
            $body = new Stream(fopen('php://temp', 'r+'));
            $body->write($exception->getMessage());
            return $response->withStatus(500)->withBody($body);
        }
    }
}
