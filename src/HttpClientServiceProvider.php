<?php

namespace KSuzuki2016\HttpClient;

use KSuzuki2016\HttpClient\Drivers\DriverInterface;
use KSuzuki2016\HttpClient\Http\HttpDuskFactory;
use KSuzuki2016\HttpClient\Http\DuskResponse;
use Symfony\Component\DomCrawler\Crawler;
use  KSuzuki2016\HttpClient\WebDriver\Driver;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Client\Response;

/**
 * Class HttpClientServiceProvider
 *
 * @mixin DuskResponse
 * @package HttpClient
 */
class HttpClientServiceProvider extends ServiceProvider
{
    public $bindings = [
        Factory::class => HttpDuskFactory::class,
        DriverInterface::class => Driver::class,
    ];

    public $singletons = [
        Response::class => DuskResponse::class,
    ];

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/http-client.php' => config_path('http-client.php'),
            ], 'config');
        }

        Response::macro('crawler', function () {
            return new Crawler($this->body());
        });
        Response::macro('stacks', function () {
            return json_decode($this->header('stacks') ?? '[]', true);
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/http-client.php', 'http-client');
    }
}
