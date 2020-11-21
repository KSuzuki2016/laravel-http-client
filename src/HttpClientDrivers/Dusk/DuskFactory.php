<?php


namespace KSuzuki2016\HttpClient\HttpClientDrivers\Dusk;


use KSuzuki2016\HttpClient\Http\Client\HttpClientFactory;

class DuskFactory extends HttpClientFactory
{
    protected function newPendingRequest()
    {
        return new DuskPendingRequest($this);
    }
}
