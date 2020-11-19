<?php


namespace KSuzuki2016\HttpClient\Extentions\HttpClients;

use Illuminate\Support\Facades\Http;
use  KSuzuki2016\HttpClient\Extentions\Contracts\HttpClient;
use Illuminate\Http\Client\Response;

class GuzzleForm extends HttpClient
{
    public function send($method, $url, $parameters = []): Response
    {
        return Http::asForm()->{$method}($url, $parameters);
    }
}
