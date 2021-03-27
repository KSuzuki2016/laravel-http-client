<?php

namespace KSuzuki2016\HttpClient;

use Illuminate\Support\Manager;
use KSuzuki2016\HttpClient\Http\Client\HttpClientFactory;
use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Factory as DuskFactory;
use KSuzuki2016\HttpClient\HttpClientDrivers\Guzzle\Factory as GuzzleFactory;

/**
 * Class DriverManager
 * @package KSuzuki2016\HttpClient
 */
class DriverManager extends Manager
{
    public function createDuskDriver(): HttpClientFactory
    {
        return $this->createDuskChromeDriver();
    }

    public function createDuskChromeDriver(): HttpClientFactory
    {
        return new DuskFactory;
    }

    public function createGuzzleDriver(): HttpClientFactory
    {
        return new GuzzleFactory;
    }

    public function getDefaultDriver(): string
    {
        return $this->config->get('http-client.driver', 'guzzle');
    }
}
