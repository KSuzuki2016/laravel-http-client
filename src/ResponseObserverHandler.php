<?php

namespace KSuzuki2016\HttpClient;

use Illuminate\Support\Collection;
use KSuzuki2016\HttpClient\Contracts\ResponseObserver;
use KSuzuki2016\HttpClient\Http\Client\HttpClientResponse;

class ResponseObserverHandler
{
    /**
     * @var HttpClientResponse
     */
    protected $response;

    /**
     * @var Collection
     */
    protected $observers;

    public function __construct(HttpClientResponse $response, Collection $observers)
    {
        $this->response = $response;
        $this->observers = $observers;
    }

    public function handle(ResponseObserver $responseObserver)
    {
        if ($this->response->successful()) {
            $this->setResponse($responseObserver->successful($this->response));
        }
        if ($this->response->failed()) {
            $this->setResponse($responseObserver->failed($this->response));
        }
        return $responseObserver->getObservation();
    }

    /**
     * @param HttpClientResponse $response
     */
    public function setResponse($response): void
    {
        if ($response instanceof HttpClientResponse) {
            $this->response = $response;
        }
    }

    public function fire(): HttpClientResponse
    {
        $this->observers->each(function (ResponseObserver $observer) {
            return $this->handle($observer);
        });
        return $this->response;
    }

    public static function make(HttpClientResponse $response, Collection $observers): self
    {
        return new static($response, $observers);
    }

}
