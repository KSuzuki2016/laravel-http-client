<?php

namespace KSuzuki2016\HttpClient;

use Illuminate\Http\Client\Factory;
use KSuzuki2016\HttpClient\Commands\HttpActionMakeCommand;
use KSuzuki2016\HttpClient\Commands\HttpMacroMakeCommand;
use Illuminate\Support\ServiceProvider;
use KSuzuki2016\HttpClient\Commands\HttpObserverMakeCommand;

/**
 * Class HttpClientServiceProvider
 */
class HttpClientServiceProvider extends ServiceProvider
{
    public $bindings = [
        'http-client' => DriverManager::class
    ];

    public $singletons = [
    ];

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/http-client.php' => config_path('http-client.php'),
            ], 'config');
        }
        if ($this->app['config']->get('http-client.http_facade_overwrite')) {
            $this->app->bind(Factory::class, function () {
                return app(DriverManager::class);
            });
        }

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/http-client.php', 'http-client');
        $this->app->singleton(DriverManager::class, function ($app) {
            return new DriverManager($app);
        });
        if ($this->app->runningInConsole()) {
            $this->commands([
                HttpMacroMakeCommand::class,
                HttpObserverMakeCommand::class,
            ]);
        }
    }
}
