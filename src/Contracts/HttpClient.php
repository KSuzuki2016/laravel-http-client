<?php


namespace KSuzuki2016\HttpClient\Contracts;


use KSuzuki2016\HttpClient\DriverManager;
use KSuzuki2016\HttpClient\Logging\HttpClientLogger;
use Illuminate\Http\Client\Response;

abstract class HttpClient implements HttpClientInterface
{
    protected $driver;

    protected $client;

    public function __construct(DriverManager $manager)
    {
        $this->client = $manager->driver($this->driver);
    }

    public function __invoke($method, $url, $parameters = []): Response
    {
        return $this->request($method, $url, $parameters);
    }

    public function request($method, $url, $parameters = []): Response
    {
        return $this->send($method, $url, $parameters);
    }

    abstract function send($method, $url, $parameters = []): Response;

}
