<?php


namespace Tests\Macros;


use KSuzuki2016\HttpClient\Drivers\ChromeBrowser;

class ReplaceTextMacro
{
    protected $replace_text;

    /**
     * DuskMacro constructor.
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
