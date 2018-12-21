<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/12/3
 * Time: 下午5:45
 */

namespace App\OAuth\Repositories;


use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Simple\RedisCache;

class Repositories
{
    /** @var \Aws\DynamoDb\DynamoDbClient */
    protected $dynamoDB;

    /** @var RedisCache */
    protected $cache;

    protected $settings = [];

    public function __construct(ContainerInterface $container)
    {
        $this->dynamoDB = $container->get('dynamoDB');
        $this->cache    = $container->get('cache');
        $this->settings = $container->get('settings')['oauth'];
    }

    protected function getOAuthCacheKey(string $key): string
    {
        switch (get_class($this)) {
            case AccessTokenRepository::class:
                $key = 'oauth-access_token-' . $key;
                break;
            case AuthCodeRepository::class:
                $key = 'oauth-auth_code-' . $key;
                break;
            default:
                $key = 'oauth-' . $key;
        }
        return $key;
    }
}
