<?php

use Symfony\Component\DomCrawler\Crawler;

return [

    'crawler' => env('HTTP_RESPONSE_CRAWLER', Crawler::class),

    /*
    |--------------------------------------------------------------------------
    | Http Request Driver
    |--------------------------------------------------------------------------
    | dusk is alias for dusk-chrome
    |
    | Drivers: "guzzle", "dusk" , "dusk-chrome"
    |
    */
    'default' => env('HTTP_CLIENT_DRIVER', 'guzzle'),

    'http_facade_overwrite' => env('HTTP_FACADE_OVERWRITE', false),

];
