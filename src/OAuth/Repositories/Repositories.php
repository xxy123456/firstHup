<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/12/3
 * Time: 下午5:45
 */

namespace App\OAuth\Repositories;


use Psr\Container\ContainerInterface;

class Repositories
{
    protected $container;

    /** @var \Aws\DynamoDb\DynamoDbClient */
    protected $dynamoDB;

    protected $settings = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->dynamoDB = $container->get('dynamoDB');
        $this->settings = $this->container->get('settings')['oauth'];
    }
}
