<?php

namespace KSuzuki2016\HttpClient\Http\Client\Extensions;

use Illuminate\Support\Collection;
use KSuzuki2016\HttpClient\Http\Client\HttpClientResponse;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Trait ResponseSchema
 *
 * @mixin HttpClientResponse
 * @package KSuzuki2016\HttpClient\Http\Client\Extensions
 */
trait ResponseSchema
{
    public function schema(bool $entity_decode = true): Collection
    {
        return collect((new Crawler($this->body()))
            ->filter('script[type="application/ld+json"]')
            ->each(function ($node) use ($entity_decode) {
                return transform($entity_decode, static fn($entity_decode) => html_entity_decode($node->text()), $node->text());
            }))->map(fn($json) => collect(json_decode($json, true)));
    }

}
