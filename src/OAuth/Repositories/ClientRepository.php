<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/11/9
 * Time: 下午5:08
 */

namespace App\OAuth\Repositories;


use App\OAuth\Entities\ClientEntity;
use Aws\DynamoDb\Marshaler;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository extends Repositories implements ClientRepositoryInterface
{
    public function getClientEntity(
        $clientIdentifier,
        $grantType = null,
        $clientSecret = null,
        $mustValidateSecret = true
    ): ClientEntity
    {
        $client = new ClientEntity();

        if ($grantType == 'password') {
            $client->setIdentifier($clientIdentifier);
            $client->setClientScopes(['basic']);
            return $client;
        }

        $marshaler = new Marshaler();
        $queryArray = [
            'client_id' => $clientIdentifier
        ];
        $itemQuery = $marshaler->marshalItem($queryArray);
        $item = $this->dynamoDB->getItem([
            'TableName' => 'OAuth_Clients',
            'Key'       => $itemQuery
        ]);
        if ($item->count()) {
            if ($mustValidateSecret && $item->toArray()['Item']['client_secret']['S'] !== $clientSecret) {
                throw OAuthServerException::invalidClient();
            }
            $client->setIdentifier($clientIdentifier);
            $client->setClientScopes($item->toArray()['Item']['scopes']);
            return $client;
        }
        throw OAuthServerException::invalidClient();
    }
}
