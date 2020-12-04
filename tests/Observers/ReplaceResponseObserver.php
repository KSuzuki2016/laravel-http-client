<?php

namespace Tests\Observers;


use KSuzuki2016\HttpClient\Contracts\ResponseObserver;
use KSuzuki2016\HttpClient\Http\Client\HttpClientResponse;

class ReplaceResponseObserver extends ResponseObserver
{
    protected $text;

    /**
     * ReplaceResponseObserver constructor.
     */
    public function __construct($text = null)
    {
        $this->text = $text;
    }

    public function successful(HttpClientResponse $response)
    {
        $response->setJson('counter', $response->json('counter', 0) + 1);
        if ($this->text) {
            $response->setJson('text', $this->text);
        }
        return $response;
    }
}
