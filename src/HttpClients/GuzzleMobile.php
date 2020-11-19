<?php


namespace KSuzuki2016\HttpClient\HttpClients;

use Illuminate\Support\Facades\Http;
use  KSuzuki2016\HttpClient\Contracts\HttpClient;
use Illuminate\Http\Client\Response;

class GuzzleMobile extends HttpClient
{
    public function send($method, $url, $parameters = null): Response
    {
        return Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1',
        ])->{$method}($url, $parameters ? $parameters : null);
    }

}
