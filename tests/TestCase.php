<?php

namespace Tests;

use KSuzuki2016\HttpClient\DriverManager;
use KSuzuki2016\HttpClient\HttpClientServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Tests\Server\CreateLocalServer;

class TestCase extends Orchestra
{
    use CreateLocalServer;

    public $host = 'localhost:8000';

    public $html_page;

    public $json;

    public $manager;

    public function setUp(): void
    {
        $this->baseUrl = 'http://' . $this->host;
        $this->createLocalServer(__DIR__ . '/Server/pages');
        parent::setUp();
        $this->html_page = $this->baseUrl . '/static.html';
        $this->json = $this->baseUrl . '/test.json';
        $this->manager = $this->app->make(DriverManager::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            HttpClientServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
