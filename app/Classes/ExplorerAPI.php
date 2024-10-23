<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class ExplorerAPI
{
    public function getWalletBalances($address)
    {
        $params = [
            'module' => 'account',
            'action' => 'balance',
            'address' => $address
        ];
        $output = [];

        foreach (config('explorers') as $chain_id => $explorer) {
            $url = $explorer['url'].'/api?apikey='.$explorer['key'];
            $response = Http::get($url.'&'.http_build_query($params));

            if ($response['status'] == 0) {
                continue;
            }

            $coingecko = Http::get('https://api.coingecko.com/api/v3/simple/price?ids='.$explorer['coingecko'].'&vs_currencies=usd');
            $usd_price = $coingecko->collect($explorer['coingecko'].'.usd');
            $balance = $this->wei2eth($response['result']);

            $output[$chain_id] = [
                'balance' => $balance,
                'usd' => $usd_price->count() ? round($balance * $usd_price[0], 2) : 0
            ];
        }

        return $output;
    }

    public function wei2eth($wei)
    {
        return bcdiv($wei, '1000000000000000000', 18);
    }
}
