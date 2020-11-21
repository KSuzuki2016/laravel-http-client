<?php


namespace KSuzuki2016\HttpClient;

use Illuminate\Support\Manager;
use KSuzuki2016\HttpClient\Http\Client\HttpClientFactory;
use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\DuskFactory;

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
        return new HttpClientFactory;
    }

    public function getDefaultDriver()
    {
        return $this->config->get('http-client.driver', 'guzzle');
    }
}
