<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class PolygonScanApi
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
        dump($this->url.'&'.implode('&', $params));
        dump($this->url.'&'.http_build_query($params));
        dump($response);
        dump($response['result']);
        dump($this->wei2eth($response['result']));
    }

    public function getInternalTransactions($address)
    {
        $params = [
            'module' => 'account',
            'action' => 'txlistinternal',
            'address' => $address,
            'startblock' => '0',
            'endblock' => '99999999',
            'page' => '1',
            'sort' => 'asc',
        ];
        $response = Http::get($this->url.'&'.http_build_query($params));
        dump($this->url.'&'.implode('&', $params));
        dump($this->url.'&'.http_build_query($params));
        dump($response);
        dump($response['result']);
        dump($this->wei2eth(250000000000000));
    }

    public function wei2eth($wei)
    {
        return bcdiv($wei, '1000000000000000000', 18);
    }

    public function getUrl()
    {
        return config('polygon.url').'/api?apikey='.config('polygon.key');
    }
}
