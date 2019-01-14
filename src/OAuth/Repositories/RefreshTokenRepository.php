<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/11/9
 * Time: 下午5:18
 */

namespace App\OAuth\Repositories;


use App\OAuth\Entities\RefreshTokenEntity;
use Aws\DynamoDb\Marshaler;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository extends Repositories implements RefreshTokenRepositoryInterface
{
    public function getNewRefreshToken(): RefreshTokenEntity
    {
        return new RefreshTokenEntity();
    }

    /** @param RefreshTokenEntityInterface|RefreshTokenEntity $refreshTokenEntity */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        echo 3;
        exit;
        $marshaler = new Marshaler();
        $item = $marshaler->marshalItem([
            'refresh_token'     => $refreshTokenEntity->getIdentifier(),
            'access_token'      => $refreshTokenEntity->getAccessToken()->getIdentifier(),
            'time_to_live'      => $refreshTokenEntity->getTimestampByTTL($this->settings['dateTimeZone'])
        ]);
        $params = [
            'TableName' => 'OAuth_RefreshTokens',
            'Item'      => $item
        ];
        $this->dynamoDB->putItem($params);
    }

    public function revokeRefreshToken($tokenId): void
    {
        $marshaler = new Marshaler();
        $itemQuery = $marshaler->marshalItem([
            'refresh_token' => $tokenId
        ]);
        $data = $marshaler->marshalItem([
            ':timeToLive' => time()
        ]);

        $this->dynamoDB->updateItem([
            'TableName'                 => 'OAuth_RefreshTokens',
            'Key'                       => $itemQuery,
            'UpdateExpression'          => 'SET time_to_live = :timeToLive',
            'ExpressionAttributeValues' => $data
        ]);
    }

    public function isRefreshTokenRevoked($tokenId): bool
    {
        $marshaler = new Marshaler();
        $itemQuery = $marshaler->marshalItem([
            'refresh_token' => $tokenId
        ]);
        $item = $this->dynamoDB->getItem([
            'TableName' => 'OAuth_RefreshTokens',
            'Key'       => $itemQuery
        ]);
        if ($item->count() && $item->toArray()['Item']['time_to_live']['N'] > time()) {
            return false;
        }
        return true;
    }
}
