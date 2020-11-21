<?php

namespace KSuzuki2016\HttpClient\Tests;

use KSuzuki2016\HttpClient\Tests\Macros\DuskMacro;

class HttpClientTest extends TestCase
{
    /** @test */
    public function guzzle_html_request()
    {
        $res = $this->client->get($this->html_page);
        self::assertSame($res->crawler()->filterXPath('//*[@id="main"]')->text(), "Static HTML");
    }

    /** @test */
    public function dusk_html_request()
    {
        $res = $this->client->dusk()->get($this->html_page);
        self::assertSame($res->crawler()->filterXPath('//*[@id="main"]')->text(), "Changed HTML");
    }

    /** @test */
    public function dusk_javascript_macro()
    {
        $res = $this->client->dusk(new DuskMacro)->get($this->html_page);
        self::assertSame($res->crawler()->filterXPath('//*/h1')->text(), "Rewrite Header");
    }

    /** @test */
    public function json_request()
    {
        $res = $this->client->get($this->json);
        self::assertSame($res->json('text'), "Json Data");
    }
}
