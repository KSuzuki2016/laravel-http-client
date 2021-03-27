<?php

namespace Tests\Observers;


use KSuzuki2016\HttpClient\Contracts\ResponseObserver;
use KSuzuki2016\HttpClient\Http\Client\HttpClientResponse;

/**
 * Class ReplaceResponseObserver
 * @package Tests\Observers
 */
class ReplaceResponseObserver extends ResponseObserver
{
    /**
     * @var null
     */
    protected $text;

    /**
     * ReplaceResponseObserver constructor.
     * @param null $text
     */
    public function __construct($text = null)
    {
        $this->text = $text;
    }

    /**
     * @param HttpClientResponse $response
     * @return HttpClientResponse
     */
    public function successful(HttpClientResponse $response): HttpClientResponse
    {
        $response->setJson('counter', $response->json('counter', 0) + 1);
        if ($this->text) {
            $response->setJson('text', $this->text);
        }
        return $response;
    }
}
