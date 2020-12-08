<?php
namespace KSuzuki2016\HttpClient\HttpClientDrivers\Dusk;

use GuzzleHttp\ClientInterface;
use KSuzuki2016\HttpClient\Http\Client\HttpClientPendingRequest;

class PendingRequest extends HttpClientPendingRequest
{

    public function getClient(): ClientInterface
    {
        return (new Client([
            'handler' => $this->buildHandlerStack(),
            'cookies' => true,
        ]))->setBrowserCallbacks($this->getBrowserCallbacks());
    }
}
