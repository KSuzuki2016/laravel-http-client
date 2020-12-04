<?php

namespace Tests;

use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\DuskFactory;
use Tests\Macros\ReplaceTextMacro;

class HttpDuskClientTest extends TestCase
{
    /** @test */
    public function check_factory()
    {
        self::assertInstanceOf(DuskFactory::class, $this->manager->driver('dusk'));
    }

    /** @test */
    public function check_http_request()
    {
        $response = $this->manager->driver('dusk')->get($this->html_page);
        self::assertTrue($response->successful());
        self::assertSame("Changed HTML", $response->crawler()->filterXPath('//*[@id="main"]')->text());
    }

    /** @test */
    public function check_script_macro()
    {
        $text = 'Replaced Text';
        $title = 'Static HTML Title';
        $response = $this->manager->driver('dusk')->browserCallback(new ReplaceTextMacro($text))->get($this->html_page);
        self::assertSame($text, $response->crawler()->filterXPath('//*/h1')->text());
        self::assertSame([$title], $response->stack());
    }


}
