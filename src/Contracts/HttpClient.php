<?php


namespace KSuzuki2016\HttpClient\Contracts;


use Illuminate\Http\Client\Response;
use KSuzuki2016\HttpClient\DriverManager;

/**
 * Class HttpClient
 * @package KSuzuki2016\HttpClient\Contracts
 */
abstract class HttpClient implements HttpClientInterface
{
    /**
     * @var
     */
    protected $driver;

    /**
     * @var mixed
     */
    protected $client;

    public function __construct(DriverManager $manager)
    {
        $this->client = $manager->driver($this->driver);
    }

    /**
     * @param $method
     * @param $url
     * @param array $parameters
     * @return Response
     */
    public function __invoke($method, $url, $parameters = []): Response
    {
        return $this->request($method, $url, $parameters);
    }

    /**
     * @param $method
     * @param $url
     * @param array $parameters
     * @return Response
     */
    public function request($method, $url, $parameters = []): Response
    {
        return $this->send($method, $url, $parameters);
    }

    /**
     * @param $method
     * @param $url
     * @param array $parameters
     * @return Response
     */
    abstract function send($method, $url, $parameters = []): Response;

}
