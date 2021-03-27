<?php


namespace KSuzuki2016\HttpClient\HttpClients;


use Illuminate\Http\Client\Response;
use KSuzuki2016\HttpClient\Contracts\HttpClient;

/**
 * Class DefaultClient
 * @package KSuzuki2016\HttpClient\HttpClients
 */
class DefaultClient extends HttpClient
{
    /**
     * @var string
     */
    protected $driver = 'dusk';

    /**
     * @param $method
     * @param $url
     * @param null $parameters
     * @return Response
     */
    public function send($method, $url, $parameters = null): Response
    {
        return $this->client->stubUrl('*', static function ($request, $options) {
        })->$method($url, $parameters ?: null);

        //return $this->client->$method($url, $parameters ? $parameters : null) ;
    }
}
