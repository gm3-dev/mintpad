<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class EtherScanApi
{
    public $url;

    public function __construct()
    {
        $this->url = $this->getUrl();
    }

    public function getBalance($address)
    {
        $params = [
            'module' => 'account',
            'action' => 'balance',
            'address' => $address
        ];
        $response = Http::get($this->url.'&'.http_build_query($params));

        if ($response['status'] == 0) {
            return false;
        }

        return $this->wei2eth($response['result']);
    }

    public function wei2eth($wei)
    {
        return bcdiv($wei, '1000000000000000000', 18);
    }

    public function getUrl()
    {
        return config('etherscan.url').'/api?apikey='.config('etherscan.key');
    }
}
