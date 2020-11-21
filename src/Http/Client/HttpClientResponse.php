<?php


namespace KSuzuki2016\HttpClient\Http\Client;

use Illuminate\Http\Client\Response;

class HttpClientResponse extends Response
{
    public $cookies;

    public $transferStats;

    public function crawler()
    {
        return app(config('http-client.crawler'), ['node' => $this->body()]);
    }

    public function stacks()
    {
        return json_decode($this->header('stacks') ?? '[]', true);
    }


}
