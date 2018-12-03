<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/11/10
 * Time: 上午8:36
 */

namespace App\OAuth\Entities;


use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ClientEntity implements ClientEntityInterface
{
    use EntityTrait, ClientTrait;

    protected $scopes = [];

    public function getClientScopes(): array
    {
        return $this->scopes;
    }

    public function setClientScopes(array $scopes): void
    {
        $this->scopes = $scopes;
    }
}
