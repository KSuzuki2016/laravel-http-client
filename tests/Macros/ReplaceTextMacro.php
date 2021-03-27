<?php


namespace Tests\Macros;


use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Browser\Chrome\ChromeBrowser;

/**
 * Class ReplaceTextMacro
 * @package Tests\Macros
 */
class ReplaceTextMacro
{
    /**
     * @var null
     */
    protected $replace_text;

    /**
     * DuskMacro constructor.
     * @param null $text
     */
    public function __construct($text = null)
    {
        $this->replace_text = $text;
    }

    /**
     * @param ChromeBrowser $browser
     * @return void|string|null
     */
    public function __invoke(ChromeBrowser $browser)
    {
        $browser->ensurejQueryIsAvailable();
        $browser->getDriver()->executeScript('$("h1").html("' . $this->replace_text . '")');
        return $browser->getDriver()->executeScript('return $("title").html() ;');
    }
}
