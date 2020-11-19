<?php


namespace KSuzuki2016\HttpClient\HttpClients;

use Illuminate\Support\Facades\Http;
use  KSuzuki2016\HttpClient\Contracts\HttpClient;
use Illuminate\Http\Client\Response;

class GuzzleForm extends HttpClient
{
    public function send($method, $url, $parameters = []): Response
    {
        return Http::asForm()->{$method}($url, $parameters);
    }
}
