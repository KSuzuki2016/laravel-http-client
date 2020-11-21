<?php


namespace KSuzuki2016\HttpClient\HttpClients;


use Illuminate\Http\Client\Response;
use KSuzuki2016\HttpClient\Contracts\HttpClient;

class DefaultClient extends HttpClient
{
    protected $driver = 'dusk';

    public function send($method, $url, $parameters = null): Response
    {
        return $this->client->stubUrl('*', function ($request, $options) {
            dump([$request, $options]);
        })->$method($url, $parameters ? $parameters : null);

        //return $this->client->$method($url, $parameters ? $parameters : null) ;
    }
}
