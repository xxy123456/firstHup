<?php
/**
 * Created by PhpStorm.
 * User: hooklife
 * Date: 2018/12/21
 * Time: 下午5:16
 */

namespace App\Dynamodb;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;

/**
 * Class DynamoDBBuilder
 *
 * @method DynamoDBBuilder setExpressionAttributeNames(array $mapping)
 * @method DynamoDBBuilder setKeyConditionExpression(string $expression)
 * @method DynamoDBBuilder setFilterExpression(string $expression)
 * @method DynamoDBBuilder setUpdateExpression(string $expression)
 * @method DynamoDBBuilder setAttributeUpdates(array $updates)
 * @method DynamoDBBuilder setConsistentRead(bool $consistent)
 * @method DynamoDBBuilder setScanIndexForward(bool $forward)
 * @method DynamoDBBuilder setExclusiveStartKey(mixed $key)
 * @method DynamoDBBuilder setReturnValues(string $type)
 * @method DynamoDBBuilder setTableName(string $table)
 * @method DynamoDBBuilder setIndexName(string $index)
 * @method DynamoDBBuilder setSelect(string $select)
 * @method DynamoDBBuilder setItem(array $item)
 * @method DynamoDBBuilder setKeys(array $keys)
 * @method DynamoDBBuilder setLimit(int $limit)
 * @method DynamoDBBuilder setKey(array $key)
 * @method DynamoDBBuilder setProjectionExpression(string $expression)
 */
class DynamoDBBuilder
{
    private static $config;
    public $query = [];
    private $dynamodbClient;
    private $marshaler;

    public function __construct()
    {
        $this->dynamodbClient = new DynamoDbClient(self::$config);
        $this->marshaler = new Marshaler();
    }

    public static function config($config, $connect = "default")
    {
        self::$config = $config[$connect]["S3Config"];
    }

    public static function table($tableName)
    {
        return (new self())->setTableName($tableName);
    }

    public function setExpressionAttributeValues($expression)
    {
        $this->query["ExpressionAttributeValues"] = $this->marshaler->marshalItem($expression);
        return $this;
    }

    /**
     * @param DynamoDbClient|null $client
     * @return ExecutableQuery
     */
    public function prepare(): ExecutableQuery
    {
        $raw = new RawDynamoDbQuery(null, $this->query);
        return new ExecutableQuery($this->dynamodbClient, $raw->finalize()->query);
    }

    /**
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (strpos($method, 'set') === 0) {
            $key = array_reverse(explode('set', $method, 2))[0];
            $this->query[$key] = current($parameters);
            return $this;
        }
        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.',
            static::class,
            $method
        ));
    }
}