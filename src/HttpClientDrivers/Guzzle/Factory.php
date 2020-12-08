<?php


namespace KSuzuki2016\HttpClient\HttpClientDrivers\Guzzle;

use KSuzuki2016\HttpClient\Http\Client\HttpClientFactory;
use KSuzuki2016\HttpClient\Http\Client\HttpClientPendingRequest;

class Factory extends HttpClientFactory
{
    protected function callPendingRequest(): HttpClientPendingRequest
    {
        return new PendingRequest($this);
    }
}
