<?php

namespace KSuzuki2016\HttpClient\HttpClientDrivers\Dusk;

use KSuzuki2016\HttpClient\Http\Client\HttpClientPendingRequest;
use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\DuskClient as Client;
use GuzzleHttp\ClientInterface;

class DuskPendingRequest extends HttpClientPendingRequest
{

    public function buildClient(): ClientInterface
    {
        return (new Client([
            'handler' => $this->buildHandlerStack(),
            'cookies' => true,
        ]))->setBrowserCallbacks(optional($this->factory)->browserCallbacks);
    }
}
