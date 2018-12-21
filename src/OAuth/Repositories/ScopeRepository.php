<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/11/9
 * Time: 下午5:13
 */

namespace App\OAuth\Repositories;


use App\OAuth\Entities\ClientEntity;
use App\OAuth\Entities\ScopeEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository extends Repositories implements ScopeRepositoryInterface
{
    const SCOPES = [
        'basic'         => [
            'description' => 'Basic details about you'
        ],
        'customer_list' => [
            'description' => 'You customer list'
        ]
    ];

    public function getScopeEntityByIdentifier($identifier): ScopeEntity
    {
        if (!isset(self::SCOPES[$identifier])) {
            throw OAuthServerException::invalidScope($identifier);
        }
        $scope = new ScopeEntity();
        $scope->setIdentifier($identifier);
        return $scope;
    }

    /**
     * @param array $scopes
     * @param string $grantType
     * @param ClientEntityInterface|ClientEntity $clientEntity
     * @param null $userIdentifier
     * @return array
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ): array
    {
        $clientScopes = $clientEntity->getClientScopes();
        $scopesArray = ScopeEntity::getIdentifiersByEntity($scopes);
//        if ($diffScopes = array_diff($clientScopes, $scopesArray)) {
//            throw OAuthServerException::invalidScope(implode($diffScopes, ', '));
//        }
        return $scopes;
    }
}
