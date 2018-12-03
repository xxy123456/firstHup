<?php
/**
 * Created by PhpStorm.
 * User: hooklife
 * Date: 2018/12/3
 * Time: 下午2:50
 */

function env(string $key, $default = null){
    $value = getenv($key);

    if ($value === false) {
        return $default;
    }
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return;
    }

    // remove quotes
    if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
        return substr($value, 1, -1);
    }

    return $value;
}
