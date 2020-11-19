<?php


namespace KSuzuki2016\HttpClient\Interfaces;


use Illuminate\Http\Client\Response;

interface HttpClientInterface
{
    public function __invoke($method, $url, $parameters = []): Response ;

    public function request($method, $url, $parameters = []): Response;

    public function send($method, $url, $parameters = []): Response;

}
