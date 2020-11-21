<?php


namespace KSuzuki2016\HttpClient\Http\Client;


use Illuminate\Http\Client\Factory;
use Illuminate\Support\Collection;

class HttpClientFactory extends Factory
{

    /**
     * The stub callables that will handle requests.
     *
     * @var Collection
     */
    public $browserCallbacks;

    public function __construct()
    {
        parent::__construct();
        $this->browserCallbacks = new Collection;
    }

    public function browserCallback(callable $callback): self
    {
        $this->browserCallbacks->push($callback);
        return $this;
    }

}
