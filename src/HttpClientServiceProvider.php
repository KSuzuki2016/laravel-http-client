<?php

namespace KSuzuki2016\HttpClient;

use Illuminate\Http\Client\Factory;
use Illuminate\Support\ServiceProvider;
use KSuzuki2016\HttpClient\Commands\HttpMacroMakeCommand;
use KSuzuki2016\HttpClient\Commands\HttpObserverMakeCommand;

/**
 * Class HttpClientServiceProvider
 *
 * クラスの説明文
 *
 * @package KSuzuki2016\HttpClient
 */
class HttpClientServiceProvider extends ServiceProvider
{
    /**
     * @var string[]
     */
    public $bindings = [
        'http-client' => DriverManager::class
    ];

    /**
     * @var array
     */
    public $singletons = [
    ];

    public function boot(): void
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

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/http-client.php', 'http-client');
        $this->app->singleton(DriverManager::class, function ($app) {
            return new DriverManager($app);
        });
        $this->app->bind('chrome-bin-path', function ($app) {
            return $app['config']->get('http-client.binPath');
        });
        if ($this->app->runningInConsole()) {
            $this->commands([
                HttpMacroMakeCommand::class,
                HttpObserverMakeCommand::class,
            ]);
        }
    }
}
