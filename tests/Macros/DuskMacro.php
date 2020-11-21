<?php


namespace KSuzuki2016\HttpClient\Tests\Macros;


use  KSuzuki2016\HttpClient\WebDriver\ChromeBrowser;

class DuskMacro
{
    /**
     * @param ChromeBrowser $browser
     * @return void|string|null
     */
    public function __invoke(ChromeBrowser $browser)
    {
        $browser->ensurejQueryIsAvailable();
        $browser->getDriver()->executeScript('$("h1").html("Rewrite Header")');
    }
}
