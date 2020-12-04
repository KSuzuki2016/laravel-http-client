<?php

namespace Tests;

use KSuzuki2016\HttpClient\Http\Client\HttpClientFactory;

class HttpGuzzleClientTest extends TestCase
{
    /** @test */
    public function check_factory()
    {
        self::assertInstanceOf(HttpClientFactory::class, $this->manager->driver());
    }

    /** @test */
    public function check_http_request()
    {
        self::assertSame("Static HTML", $this->manager->get($this->html_page)->crawler()->filterXPath('//*[@id="main"]')->text());
    }

    /** @test */
    public function check_json_request()
    {
        self::assertSame("Json Data", $this->manager->get($this->json)->json('text'));
    }
}
