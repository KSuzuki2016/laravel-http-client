<?php


namespace KSuzuki2016\HttpClient\Contracts;


use KSuzuki2016\HttpClient\Http\Client\HttpClientResponse;

interface ResponseObserverInterface
{
    public function getObservation(): bool;

    /**
     * @param HttpClientResponse $response
     * @return void | HttpClientResponse
     */
    public function successful(HttpClientResponse $response);

    /**
     * @param HttpClientResponse $response
     * @return void | HttpClientResponse
     */
    public function failed(HttpClientResponse $response);
}
