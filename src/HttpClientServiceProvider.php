<?php

namespace HttpClient ;

use duncan3dc\Laravel\Drivers\DriverInterface;
use HttpClient\Interfaces\RouterInterface;
use HttpClient\Repositories\RouteCollection;
use HttpClient\Repositories\Router;
use HttpClient\Repositories\RouteResource;
use Illuminate\Foundation\Application;
use Symfony\Component\DomCrawler\Crawler ;
use HttpClient\WebDriver\Driver;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Client\Response ;
/**
 * Class HttpClientServiceProvider
 *
 * @mixin DuskResponse
 * @package HttpClient
 */
class HttpClientServiceProvider extends ServiceProvider
{
    public $bindings = [
        Factory::class              => DuskRequest::class ,
        DriverInterface::class      => Driver::class ,
        RouterInterface::class      => Router::class ,
    ];

    public $singletons = [
        Response::class             => DuskResponse::class ,
        RouteCollection::class      => RouteCollection::class ,
    ];

    public function boot()
    {
        // HttpCommandExecutor::DEFAULT_HTTP_HEADERS

        Response::macro('crawler' , function (){
            return new Crawler( $this->body() ) ;
        }) ;
        Response::macro('stacks' , function (){
            return json_decode( $this->header('stacks')??'[]' , true ) ;
        }) ;
    }
    public function register()
    {
        $this->app->singleton(RouteResource::class,function(Application $app ){
            return new RouteResource( $app->make(Router::class) ) ;
        });
    }
}
