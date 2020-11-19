<?php


namespace KSuzuki2016\HttpClient\Extentions\HttpClients;


use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use KSuzuki2016\HttpClient\Extentions\Interfaces\HttpClientInterface;

class DefaultClient implements HttpClientInterface
{
    public function __invoke($method, $url, $parameters = []): Response
    {
        return $this->request($method, $url, $parameters);
    }

    public function request($method, $url, $parameters = []): Response
    {
        return $this->send($method, $url, $parameters);
    }

    public function send($method, $url, $parameters = null): Response
    {
        return Http::$method($url, $parameters ? $parameters : null);
    }
}
