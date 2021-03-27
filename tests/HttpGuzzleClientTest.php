<?php

namespace Tests;

use KSuzuki2016\HttpClient\HttpClientDrivers\Guzzle\Factory;

/**
 * Class HttpGuzzleClientTest
 * @package Tests
 */
class HttpGuzzleClientTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function check_factory(): void
    {
        self::assertInstanceOf(Factory::class, $this->manager->driver());
    }

    /**
     * @test
     * @return void
     */
    public function check_http_request(): void
    {
        self::assertSame("Static HTML", $this->manager->get($this->html_page)->crawler()->filterXPath('//*[@id="main"]')->text());
    }

    /**
     * @test
     * @return void
     */
    public function check_json_request(): void
    {
        self::assertSame("Json Data", $this->manager->get($this->json)->json('text'));
    }
}
