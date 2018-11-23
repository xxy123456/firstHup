<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/11/10
 * Time: 上午8:59
 */

namespace App\OAuth\Repositories;


use App\OAuth\Entities\AuthCodeEntity;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
//        return $authCodeEntity;
    }

    public function revokeAuthCode($codeId)
    {

    }

    public function isAuthCodeRevoked($codeId)
    {
        return false;
    }
}