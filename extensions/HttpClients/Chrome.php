<?php


namespace KSuzuki2016\HttpClient\HttpClients;

use Exception;
use Illuminate\Http\Client\Response;
use KSuzuki2016\HttpClient\Contracts\HttpClient;
use KSuzuki2016\HttpClient\HttpClients\Macros\BrowserMacro;

/**
 * Class Chrome
 * @package KSuzuki2016\HttpClient\HttpClients
 */
class Chrome extends HttpClient
{

    /**
     * @param $method
     * @param $url
     * @param array $parameters
     * @return Response
     * @throws Exception
     */
    public function send($method, $url, $parameters = []): Response
    {
        if ($method === 'get') {
            $this->client->dusk(new BrowserMacro);
            return $this->client->get($url);
        }
        throw new Exception('this client GET method only ');
    }

}
