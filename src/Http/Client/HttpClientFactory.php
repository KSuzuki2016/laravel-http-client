<?php


namespace KSuzuki2016\HttpClient\Http\Client;


use Illuminate\Http\Client\Factory;
use Illuminate\Support\Collection;
use KSuzuki2016\HttpClient\Contracts\ResponseObserver;

class HttpClientFactory extends Factory
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

    protected function newPendingRequest()
    {
        return new HttpClientPendingRequest($this);
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

}
