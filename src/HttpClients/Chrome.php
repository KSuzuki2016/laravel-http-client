<?php


namespace KSuzuki2016\HttpClient\HttpClients;

use KSuzuki2016\HttpClient\Contracts\HttpClient;
use KSuzuki2016\HttpClient\HttpClients\Macros\BrowserMacro;
use Illuminate\Http\Client\Response;
use Exception;

class Chrome extends HttpClient
{

    public function send($method, $url, $parameters = []): Response
    {
        if ($method === 'get') {
            $this->client->dusk(new BrowserMacro);
            return $this->client->get($url);
        }
        throw new Exception('this client GET method only ');
    }

}
