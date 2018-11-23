<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/11/10
 * Time: 上午8:48
 */

namespace App\OAuth\Entities;


use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;

class RefreshTokenEntity implements RefreshTokenEntityInterface
{
    use RefreshTokenTrait, EntityTrait;
}