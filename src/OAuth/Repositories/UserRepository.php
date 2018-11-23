<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/11/9
 * Time: 下午5:17
 */

namespace App\OAuth\Repositories;


use App\OAuth\Entities\UserEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $user = new UserEntity();
        $user->setIdentifier(1);

        return $user;
    }
}