<?php

namespace KSuzuki2016\HttpClient\Http\Client\Extensions;

use Exception;
use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Trait ResponseCrawler
 *
 * @mixin Response
 * @package KSuzuki2016\HttpClient\Http\Client\Extensions
 */
trait ResponseCrawler
{
    /**
     * @return Crawler
     */
    public function crawler(): Crawler
    {
        try {
            return app(Crawler::class, ['node' => $this->body(), 'uri' => (string)$this->effectiveUri()]);
        } catch (Exception $e) {
            return app(Crawler::class, ['node' => $e->getMessage(), 'uri' => '/']);
        }
    }

}
