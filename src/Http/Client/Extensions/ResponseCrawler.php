<?php

namespace KSuzuki2016\HttpClient\Http\Client\Extensions;

use Illuminate\Http\Client\Response;

/**
 * Trait ResponseCrawler
 *
 * @mixin Response
 * @package KSuzuki2016\HttpClient\Http\Client\Extensions
 */
trait ResponseCrawler
{
    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function crawler()
    {
        return app(config('http-client.crawler'), ['node' => $this->body(), 'uri' => (string)$this->effectiveUri()]);
    }

}
