<?php

namespace KSuzuki2016\HttpClient\Http\Client\Extensions;

use KSuzuki2016\HttpClient\Http\Client\HttpClientResponse;

/**
 * Trait ResponseCrawler
 *
 * @mixin HttpClientResponse
 * @package KSuzuki2016\HttpClient\Http\Client\Extensions
 */
trait ResponseCrawler
{
    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function crawler()
    {
        return app(config('http-client.crawler'), ['node' => $this->body()]);
    }

}
