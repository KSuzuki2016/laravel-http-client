<?php


namespace KSuzuki2016\HttpClient\HttpClients;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use KSuzuki2016\HttpClient\Contracts\HttpClient;

/**
 * Class GuzzleXMLHttp
 * @package KSuzuki2016\HttpClient\HttpClients
 */
class GuzzleXMLHttp extends HttpClient
{
    /**
     * @param $method
     * @param $url
     * @param null $parameters
     * @return Response
     */
    public function send($method, $url, $parameters = null): Response
    {
        return Http::withHeaders([
            'x-requested-with' => 'XMLHttpRequest',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36',
        ])->{$method}($method, $url, $parameters ?: null);
    }

}
