<?php

namespace KSuzuki2016\HttpClient\Logging;

use KSuzuki2016\HttpClient\Contracts\ResponseObserver;
use KSuzuki2016\HttpClient\Http\Client\HttpClientResponse;

/**
 * Class ResponseLogObserver
 * @package KSuzuki2016\HttpClient\Logging
 */
class ResponseLogObserver extends ResponseObserver
{
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
        $this->breakObservation();
    }
}
