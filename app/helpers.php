<?php

if (!function_exists('input')) {
    function input($object, $key)
    {
        if (!is_null(old($key))) {
            return old($key);
        } elseif (isset($object->{$key})) {
            return $object->{$key};
        } else {
            return null;
        }
    }
}

if (!function_exists('shorten_address')) {
    function shorten_address($address)
    {
        return substr($address, 0, 5) . '...' . substr($address, -7);
    }
}
