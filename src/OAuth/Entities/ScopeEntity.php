<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/11/10
 * Time: 上午8:49
 */

namespace App\OAuth\Entities;


use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ScopeEntity implements ScopeEntityInterface
{
    use EntityTrait;

    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }

    public static function getIdentifiersByEntity(array $scopeEntity): array
    {
        return array_map(function ($entity): string {
            /** @var ScopeEntity $entity */
            return $entity->getIdentifier();
        }, $scopeEntity);
    }
}
