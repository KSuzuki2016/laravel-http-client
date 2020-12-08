<?php


namespace KSuzuki2016\HttpClient\Http\Client;


use Illuminate\Http\Client\Factory;
use Illuminate\Support\Collection;
use KSuzuki2016\HttpClient\Contracts\ResponseObserver;

abstract class HttpClientFactory extends Factory
{

    /**
     * The stub callables that will handle requests.
     *
     * @var Collection
     */
    public $browserCallbacks;

    /**
     * @var Collection
     */
    public $responseObserver;

    public function __construct()
    {
        parent::__construct();
        $this->browserCallbacks = new Collection;
        $this->responseObserver = new Collection;
    }

    abstract protected function callPendingRequest(): HttpClientPendingRequest;

    protected function newPendingRequest(): HttpClientPendingRequest
    {
        return $this->callPendingRequest();
    }

    public function browserCallback(callable $callback): self
    {
        $this->browserCallbacks->push($callback);
        return $this;
    }

    public function responseObserver(ResponseObserver $observer): self
    {
        $this->responseObserver->push($observer);
        return $this;
    }

    /**
     * Execute a method against a new pending request instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return tap($this->newPendingRequest(), function ($request) {
            $request->stub($this->stubCallbacks);
        })->{$method}(...$parameters);
    }

}
