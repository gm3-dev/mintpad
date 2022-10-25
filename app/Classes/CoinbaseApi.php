<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class CoinbaseAPI
{
    public $url;
    public $secret;
    public $key;
    public $phrase;

    public function __construct()
    {
        // Coinbase Pro
        $this->url = config('coinbase.url');
        $this->secret = config('coinbase.secret');
        $this->key = config('coinbase.key');
        $this->phrase = config('coinbase.phrase');
    }

    public function getProfile()
    {
        $route = '/profiles';
        $response = Http::withHeaders($this->getHeaders('GET', $route))->get($this->url.$route);
        return json_decode($response->body());
    }

    public function getAccounts()
    {
        $route = '/accounts';
        $response = Http::withHeaders($this->getHeaders('GET', $route))->get($this->url.$route);
        return json_decode($response->body());
    }

    public function getAccountById($id)
    {
        $route = '/accounts/'.$id;
        $response = Http::withHeaders($this->getHeaders('GET', $route))->get($this->url.$route);
        return json_decode($response->body());
    }

    public function getAccountByToken($token)
    {
        $accounts = $this->getAccounts();
        foreach ($accounts as $account) {
            if ($account->currency == $token) {
                return $account;
            }
        }
    }

    public function getProducts()
    {
        $route = '/products';
        $response = Http::withHeaders($this->getHeaders('GET', $route))->get($this->url.$route);
        return json_decode($response->body());
    }

    public function getProductByid($id)
    {
        $route = '/products/'.$id;
        $response = Http::withHeaders($this->getHeaders('GET', $route))->get($this->url.$route);
        return json_decode($response->body());
    }

    public function postOrder($pair, $amount)
    {
        $route = '/orders';
        $body = [
            // 'profile_id' => '3a152b0b-5b19-47bc-9b39-889a3410949a',
            'type' => 'market',
            'side' => 'sell',
            'product_id' => $pair,
            'size' => $amount
        ];
        $response = Http::withHeaders($this->getHeaders('POST', $route, $body))->post($this->url.$route, $body);
        return json_decode($response->body());
    }

    public function getCoinbaseAccounts()
    {
        $route = '/coinbase-accounts';
        $response = Http::withHeaders($this->getHeaders('GET', $route))->get($this->url.$route);
        return json_decode($response->body());
    }

    public function postConversion()
    {
        $route = '/conversions';
        $body = [
            'profile_id' => '3a152b0b-5b19-47bc-9b39-889a3410949a',
            'from' => 'SOL',
            'to' => 'USD',
            'amount' => '0.2'
        ];
        $response = Http::withHeaders($this->getHeaders('POST', $route, $body))->post($this->url.$route, $body);
        return json_decode($response->body());
    }

    public function getHeaders($method, $route, $body = '')
    {
        $body = is_array($body) ? json_encode($body) : '';
        $time = time();
        $what = $time.$method.$route.$body;
        $secret = base64_decode($this->secret);
        $hmac = hash_hmac('sha256', $what, $secret, true);
        $signature = base64_encode($hmac);

        return [
            'CB-ACCESS-KEY' => $this->key,
            'CB-ACCESS-SIGN' => $signature,
            'CB-ACCESS-TIMESTAMP' => (string) $time,
            'CB-ACCESS-PASSPHRASE' => $this->phrase,
            'Content-Type' => 'application/json',
        ];
    }
}
