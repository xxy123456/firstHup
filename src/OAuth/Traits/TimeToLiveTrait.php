<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/12/4
 * Time: 下午5:16
 */

namespace App\OAuth\Traits;

trait TimeToLiveTrait
{
    public function getTimestampByTTL($dateTimeZone = 'UTC'): int
    {
        $ttlDateTime = $this->getExpiryDateTime();
        $ttlDateTime->setTimezone(new \DateTimeZone($dateTimeZone));
        return $ttlDateTime->getTimestamp();
    }

    /**
     * @return \DateTime
     */
    abstract public function getExpiryDateTime();
}
