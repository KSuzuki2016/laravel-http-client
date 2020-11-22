<?php

namespace KSuzuki2016\HttpClient\Contracts;

use KSuzuki2016\HttpClient\Http\Client\HttpClientResponse;

abstract class ResponseObserver implements ResponseObserverInterface
{
    protected $observation = true;

    protected function breakObservation(): void
    {
        $this->observation = false;
    }

    protected function shouldObservation(): void
    {
        $this->observation = true;
    }

    /**
     * @return bool
     */
    public function getObservation(): bool
    {
        return $this->observation;
    }

    /**
     * @param HttpClientResponse $response
     * @return void | HttpClientResponse
     */
    public function successful(HttpClientResponse $response)
    {

    }

    /**
     * @param HttpClientResponse $response
     * @return void | HttpClientResponse
     */
    public function failed(HttpClientResponse $response)
    {

    }

}
