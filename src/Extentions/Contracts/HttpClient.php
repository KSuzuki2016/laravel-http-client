<?php


namespace KSuzuki2016\HttpClient\Extentions\Contracts;


use Illuminate\Http\Client\Factory;
use  KSuzuki2016\HttpClient\Extentions\Interfaces\HttpClientInterface;
use  KSuzuki2016\HttpClient\Extentions\Logging\HttpClientLogger;
use Illuminate\Http\Client\Response;

abstract class HttpClient implements HttpClientInterface
{
    protected $logger;

    protected $client;

    public function __construct()
    {
        $this->client = app(Factory::class);
        $this->logger = resolve(HttpClientLogger::class);
    }

    public function __invoke($method, $url, $parameters = []): Response
    {
        return $this->request($method, $url, $parameters);
    }

    public function request($method, $url, $parameters = []): Response
    {
        return tap($this->send($method, $url, $parameters), $this->logger->logging($method, $url, $parameters));
    }

    abstract function send($method, $url, $parameters = []): Response;

}
