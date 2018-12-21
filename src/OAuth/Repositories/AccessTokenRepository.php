<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/11/9
 * Time: 下午5:15
 */

namespace App\OAuth\Repositories;


use App\OAuth\Entities\AccessTokenEntity;
use App\OAuth\Entities\ScopeEntity;
use Aws\DynamoDb\Marshaler;
use Aws\Exception\AwsException;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository extends Repositories implements AccessTokenRepositoryInterface
{
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntity
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }

    /**
     * @param AccessTokenEntityInterface|AccessTokenEntity $accessTokenEntity
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $marshaler = new Marshaler();
        $data = [
            'access_token' => $accessTokenEntity->getIdentifier(),
            'user_id'      => $accessTokenEntity->getUserIdentifier(),
            'client_id'    => $accessTokenEntity->getClient()->getIdentifier(),
            'scopes'       => ScopeEntity::getIdentifiersByEntity($accessTokenEntity->getScopes()),
            'time_to_live' => $accessTokenEntity->getTimestampByTTL($this->settings['dateTimeZone'])
        ];
        $item = $marshaler->marshalItem($data);
        $params = [
            'TableName' => 'OAuth_AccessTokens',
            'Item'      => $item
        ];
        $this->dynamoDB->putItem($params);

        $this->cache->set(
            $this->getOAuthCacheKey($data['access_token']),
            array_only($data, ['user_id']),
            $data['time_to_live']
        );
    }

    public function revokeAccessToken($tokenId): void
    {
        $marshaler = new Marshaler();
        $itemQuery = $marshaler->marshalItem([
            'access_token' => $tokenId
        ]);
        $data = $marshaler->marshalItem([
            ':accessToken' => $tokenId,
            ':timeToLive'  => time()
        ]);
        try {
            $this->dynamoDB->updateItem([
                'TableName'                 => 'OAuth_AccessTokens',
                'Key'                       => $itemQuery,
                'UpdateExpression'          => 'SET time_to_live = :timeToLive',
                'ConditionExpression'       => 'access_token = :accessToken',
                'ExpressionAttributeValues' => $data
            ]);
        } catch (AwsException $exception) {
            $errorCode = $exception->getAwsErrorCode();
            if ($errorCode !== 'ConditionalCheckFailedException') {
                throw $exception;
            }
        }

        $this->cache->delete($this->getOAuthCacheKey($tokenId));
    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        if ($this->cache->has($this->getOAuthCacheKey($tokenId))) {
            return false;
        }

        $marshaler = new Marshaler();
        $itemQuery = $marshaler->marshalItem([
            'access_token' => $tokenId
        ]);
        $item = $this->dynamoDB->getItem([
            'TableName' => 'OAuth_AccessTokens',
            'Key'       => $itemQuery
        ]);
        if ($item->count() && $item->toArray()['Item']['time_to_live']['N'] > time()) {
            return false;
        }
        return true;
    }
}
