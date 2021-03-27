<?php


namespace KSuzuki2016\HttpClient\Contracts;


use Illuminate\Http\Client\Response;

/**
 * Interface HttpClientInterface
 * @package KSuzuki2016\HttpClient\Contracts
 */
interface HttpClientInterface
{
    /**
     * @param $method
     * @param $url
     * @param array $parameters
     * @return Response
     */
    public function __invoke($method, $url, $parameters = []): Response;

    /**
     * @param $method
     * @param $url
     * @param array $parameters
     * @return Response
     */
    public function request($method, $url, $parameters = []): Response;

    /**
     * @param $method
     * @param $url
     * @param array $parameters
     * @return Response
     */
    public function send($method, $url, $parameters = []): Response;

}
