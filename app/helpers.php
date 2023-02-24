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
    function shorten_address($address, $start = 5, $end = 7)
    {
        return substr($address, 0, $start) . '...' . substr($address, -$end);
    }
}

if (!function_exists('get_blockchains')) {
    function get_blockchains()
    {
        $blockchains = [];

        foreach (config('blockchains') as $blockchain) {
            $array_key = $blockchain['testnet'] == true ? 'Testnets' : 'Mainnets';
            $blockchains[$array_key][$blockchain['id']] = $blockchain['full'].' ('.$blockchain['token'].')';
        }

        return $blockchains;
    }
}
