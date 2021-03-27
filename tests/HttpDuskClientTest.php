<?php

namespace Tests;

use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Factory;
use Tests\Macros\DocumentHTML;
use Tests\Macros\ExceptionTraceMacro;
use Tests\Macros\ReplaceTextMacro;

/**
 * Class HttpDuskClientTest
 * @package Tests
 */
class HttpDuskClientTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function check_factory(): void
    {
        self::assertInstanceOf(Factory::class, $this->manager->driver('dusk'));
    }

    /**
     * @test
     * @return void
     */
    public function check_http_request(): void
    {
        $response = $this->manager->driver('dusk')->get($this->html_page);
        self::assertTrue($response->successful());
        self::assertSame("Changed HTML", $response->crawler()->filterXPath('//*[@id="main"]')->text());
    }

    /**
     * @test
     * @return void
     */
    public function check_script_macro(): void
    {
        $text = 'Replaced Text';
        $title = 'Static HTML Title';
        $response = $this->manager->driver('dusk')->browserCallback(new ReplaceTextMacro($text))->get($this->html_page);
        self::assertSame($text, $response->crawler()->filterXPath('//*/h1')->text());
        self::assertSame([$title], $response->stack());
    }

    /**
     * @test
     * @return void
     */
    public function open_blank_page_after_replace_html(): void
    {
        $html = file_get_contents($this->document_text);
        $response = $this->manager->driver('dusk')->browserCallback(new DocumentHTML($html))->get('about:blank');
        self::assertTrue($response->ok());
        self::assertSame('Document Text Title', $response->crawler()->filterXPath('//*/title')->text());
    }

    /**
     * @test
     * @return void
     */
    public function check_script_macro_exception(): void
    {
        $response = $this->manager->driver('dusk')->browserCallback(new ExceptionTraceMacro)->get('about:blank');
        self::assertTrue($response->failed());
        self::assertStringStartsWith('unknown error', $response->header('errors'));
    }

}
