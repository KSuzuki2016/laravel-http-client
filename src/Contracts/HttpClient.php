<?php


namespace KSuzuki2016\HttpClient\Contracts;


use KSuzuki2016\HttpClient\DriverManager;
use KSuzuki2016\HttpClient\Logging\HttpClientLogger;
use Illuminate\Http\Client\Response;

abstract class HttpClient implements HttpClientInterface
{
    protected $logger;

    protected $driver;

    protected $client;

    public function __construct(DriverManager $manager)
    {
        $this->client = $manager->driver($this->driver);
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
