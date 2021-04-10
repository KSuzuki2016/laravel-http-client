<?php

namespace KSuzuki2016\HttpClient;

use KSuzuki2016\HttpClient\Contracts\ResponseObserverInterface;
use KSuzuki2016\HttpClient\Http\Client\Collections\ResponseObserverCollection;
use KSuzuki2016\HttpClient\Http\Client\HttpClientResponse;

/**
 * Class ResponseObserverHandler
 * @package KSuzuki2016\HttpClient
 */
class ResponseObserverHandler
{
    /**
     * @var HttpClientResponse
     */
    protected $response;

    /**
     * @var ResponseObserverCollection
     */
    protected $observers;

    public function __construct(HttpClientResponse $response, ResponseObserverCollection $observers)
    {
        $this->response = $response;
        $this->observers = $observers;
    }

    public function handle(ResponseObserverInterface $responseObserver): bool
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
        $this->observers->each(function (ResponseObserverInterface $observer) {
            return $this->handle($observer);
        });
        return $this->response;
    }

    public static function make(HttpClientResponse $response, ResponseObserverCollection $observers): self
    {
        return new static($response, $observers);
    }

}
