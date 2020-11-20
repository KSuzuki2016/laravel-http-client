<?php

return [

    'response'  => env('HTTP_RESPONSE', \KSuzuki2016\HttpClient\Http\DuskResponse::class ) ,

    'crawler'   => env('HTTP_CRAWLER', \Symfony\Component\DomCrawler\Crawler::class ) ,

    'default' => env('HTTP_CLIENT', 'guzzle') ,
    'drivers' => [
        'guzzle' => [
            'driver'    => 'guzzle',
            'factory'   => \Illuminate\Http\Client\Factory::class
        ],
        'dusk' => [
            'driver' => 'dusk',
            'factory'   => \KSuzuki2016\HttpClient\Http\HttpDuskFactory::class
        ],
    ],

];
