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
        dump($response['result']);
        dump($this->wei2eth($response['result']));
    }

    public function getNormalTransactions($address)
    {
        dump('Normal');
        $params = [
            'module' => 'account',
            'action' => 'txlist',
            'address' => $address,
            'startblock' => '0',
            'endblock' => '99999999',
            'page' => '1',
            'sort' => 'asc',
        ];
        $response = Http::get($this->url.'&'.http_build_query($params));
        // dump($response['result'][0]);
        // foreach ($response['result'] as $transaction) {
        //     dump(date('Y-m-d', $transaction['timeStamp']).': '.$this->wei2eth($transaction['value']) . ' ('.$transaction['value'].') - ' . $transaction['functionName']);
        // }

        dump($response['result']);
        // dump($this->wei2eth(250000000000000));
    }

    public function getInternalTransactions($address)
    {
        dump('Internal');
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

        // foreach ($response['result'] as $transaction) {
        //     dump(date('Y-m-d', $transaction['timeStamp']).': '.$this->wei2eth($transaction['value']) . ' ('.$transaction['value'].') - ' . $transaction['functionName']);
        // }

        dump($response['result']);
        // dump($this->wei2eth(250000000000000));
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
