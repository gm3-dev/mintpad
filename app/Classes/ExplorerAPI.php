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

        foreach (config('explorers') as $chain_id => $exporer) {
            $url = $exporer['url'].'/api?apikey='.$exporer['key'];
            $response = Http::get($url.'&'.http_build_query($params));

            if ($response['status'] == 0) {
                continue;
            }

            $output[$chain_id] = $this->wei2eth($response['result']);
        }

        return $output;
    }

    public function wei2eth($wei)
    {
        return bcdiv($wei, '1000000000000000000', 18);
    }
}
