<?php


namespace KSuzuki2016\HttpClient\HttpClients;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use KSuzuki2016\HttpClient\Contracts\HttpClient;

/**
 * Class GuzzleForm
 * @package KSuzuki2016\HttpClient\HttpClients
 */
class GuzzleForm extends HttpClient
{
    /**
     * @param $method
     * @param $url
     * @param array $parameters
     * @return Response
     */
    public function send($method, $url, $parameters = []): Response
    {
        return Http::asForm()->{$method}($url, $parameters);
    }
}
