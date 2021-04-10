<?php

return [

    'binPath' => env('HTTP_CLIENT_CHROME_PATH'),
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
