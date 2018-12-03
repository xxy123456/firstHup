<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/11/10
 * Time: 上午8:59
 */

namespace App\OAuth\Repositories;


use App\OAuth\Entities\AuthCodeEntity;
use App\OAuth\Entities\ScopeEntity;
use Aws\DynamoDb\Marshaler;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository extends Repositories implements AuthCodeRepositoryInterface
{
    public function getNewAuthCode(): AuthCodeEntity
    {
        return new AuthCodeEntity();
    }

    /** @param AuthCodeEntityInterface|AuthCodeEntity $authCodeEntity */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        $marshaler = new Marshaler();
        $item = $marshaler->marshalItem([
            'auth_code'    => $authCodeEntity->getIdentifier(),
            'user_id'      => $authCodeEntity->getUserIdentifier(),
            'client_id'    => $authCodeEntity->getClient()->getIdentifier(),
            'scopes'       => ScopeEntity::getIdentifiersByEntity($authCodeEntity->getScopes()),
            'time_to_live' => $authCodeEntity->getTimestampByTTL($this->settings['dateTimeZone'])
        ]);
        $params = [
            'TableName' => 'OAuth_AuthCodes',
            'Item'      => $item
        ];
        $this->dynamoDB->putItem($params);
    }

    public function revokeAuthCode($codeId): void
    {
        $marshaler = new Marshaler();
        $itemQuery = $marshaler->marshalItem([
            'auth_code' => $codeId
        ]);
        $data = $marshaler->marshalItem([
            ':timeToLive' => time()
        ]);

        $this->dynamoDB->updateItem([
            'TableName'                 => 'OAuth_AuthCodes',
            'Key'                       => $itemQuery,
            'UpdateExpression'          => 'SET time_to_live = :timeToLive',
            'ExpressionAttributeValues' => $data
        ]);
    }

    public function isAuthCodeRevoked($codeId): bool
    {
        $marshaler = new Marshaler();
        $itemQuery = $marshaler->marshalItem([
            'auth_code' => $codeId
        ]);
        $item = $this->dynamoDB->getItem([
            'TableName' => 'OAuth_AuthCodes',
            'Key'       => $itemQuery
        ]);
        if ($item->count() && $item->toArray()['Item']['time_to_live']['N'] > time()) {
            return false;
        }
        return true;
    }
}
