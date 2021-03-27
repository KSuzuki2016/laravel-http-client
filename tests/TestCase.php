<?php

namespace Tests;

use KSuzuki2016\HttpClient\DriverManager;
use KSuzuki2016\HttpClient\HttpClientServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Tests\Server\CreateLocalServer;

/**
 * Class TestCase
 * @package Tests
 */
class TestCase extends Orchestra
{
    use CreateLocalServer;

    /**
     * @var string
     */
    public $host = 'localhost:8000';

    /**
     * @var string
     */
    public $document_text;

    /**
     * @var string
     */
    public $html_page;

    /**
     * @var string
     */
    public $json;

    /**
     * @var DriverManager|mixed
     */
    public $manager;

    public function setUp(): void
    {
        $this->baseUrl = 'http://' . $this->host;
        $this->createLocalServer(__DIR__ . '/Server/pages');
        parent::setUp();
        $this->document_text = __DIR__ . '/Server/pages/document.txt';
        $this->html_page = $this->baseUrl . '/static.html';
        $this->json = $this->baseUrl . '/test.json';
        $this->manager = $this->app->make(DriverManager::class);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            HttpClientServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
