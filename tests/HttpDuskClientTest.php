<?php

namespace Tests;

use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Factory;
use KSuzuki2016\HttpClient\Macros\DocumentHTML;
use Tests\Macros\ExceptionTraceMacro;
use Tests\Macros\ReplaceTextMacro;

class HttpDuskClientTest extends TestCase
{
    /** @test */
    public function check_factory()
    {
        self::assertInstanceOf(Factory::class, $this->manager->driver('dusk'));
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

    /** @test */
    public function open_blank_page_after_replace_html()
    {
        $html = file_get_contents($this->document_text);
        $response = $this->manager->driver('dusk')->browserCallback(new DocumentHTML($html))->get('about:blank');
        self::assertTrue($response->ok());
        self::assertSame('Document Text Title', $response->crawler()->filterXPath('//*/title')->text());
    }

    /** @test */
    public function check_script_macro_exception()
    {
        $response = $this->manager->driver('dusk')->browserCallback(new ExceptionTraceMacro)->get('about:blank');
        self::assertTrue($response->failed());
        self::assertStringStartsWith('unknown error', $response->header('errors'));
    }

}
