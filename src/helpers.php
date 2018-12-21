<?php
/**
 * Created by PhpStorm.
 * User: bailian
 * Date: 2018/12/5
 * Time: 下午6:15
 */

if (! function_exists('array_only')) {
    /**
     * Get a subset of the items from the given array.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     */
    function array_only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }
}
