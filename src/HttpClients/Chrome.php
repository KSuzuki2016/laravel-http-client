<?php


namespace KSuzuki2016\HttpClient\HttpClients;

use Illuminate\Support\Facades\Http;
use  KSuzuki2016\HttpClient\Contracts\HttpClient;
use  KSuzuki2016\HttpClient\Macros\BrowserMacro;
use Illuminate\Http\Client\Response;
use Exception;

class Chrome extends HttpClient
{
    public function send($method, $url, $parameters = []): Response
    {
        if ($method === 'get') {
            Http::dusk(new BrowserMacro);
            return Http::get($url);
        }
        throw new Exception('this client GET method only ');
    }

}
