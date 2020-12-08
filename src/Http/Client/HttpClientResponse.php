<?php


namespace KSuzuki2016\HttpClient\Http\Client;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

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

    /** @noinspection ClassMethodNameMatchesFieldNameInspection */
    public function stacks()
    {
        if (!$this->stacks) {
            $this->stacks = json_decode('[' . $this->header('stacks') . ']', true);
        }
        return $this->stacks;
    }

    public function stack(int $key = 0)
    {
        return Arr::get($this->stacks(), $key);
    }

    public function setStacks(array $stacks): self
    {
        $this->stacks = $stacks;
        return $this;
    }

    public function setJson($key, $value = null): self
    {
        if (is_array($key)) {
            $this->decoded = $key;
        } else if (is_string($key) || is_int($key)) {
            $this->decoded[$key] = $value;
        }
        return $this;
    }

}
