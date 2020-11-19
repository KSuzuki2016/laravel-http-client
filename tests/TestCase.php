<?php

namespace KSuzuki2016\HttpClient\Tests;

use KSuzuki2016\HttpClient\HttpClientServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Http\Client\Factory;

class TestCase extends Orchestra
{

    public $url = 'localhost:8000';

    public $html_page;

    public $json;

    public $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->html_page = $this->url . '/static.html';
        $this->json = $this->url . '/test.json';
        $this->client = app(Factory::class);
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
