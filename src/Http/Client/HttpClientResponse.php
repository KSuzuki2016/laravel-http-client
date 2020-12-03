<?php


namespace KSuzuki2016\HttpClient\Http\Client;

use Illuminate\Http\Client\Response;

class HttpClientResponse extends Response
{
    public $cookies;

    protected $decoded;

    protected $stacks;

    public $transferStats;

    public function crawler()
    {
        return app(config('http-client.crawler'), ['node' => $this->body()]);
    }

    public function stacks()
    {
        if (!$this->stacks) {
            $this->stacks = json_decode($this->header('stacks') ?? '[]', true);
        }
        return $this->stacks;
    }

    public function setStacks(array $stacks)
    {
        $this->stacks = $stacks;
    }

    public function setDecoded(array $decoded)
    {
        $this->decoded = $decoded;
    }

}
