<?php


namespace KSuzuki2016\HttpClient\HttpClients\Macros;


use KSuzuki2016\HttpClient\Drivers\ChromeBrowser;

/**
 * Class BrowserMacro
 * @package KSuzuki2016\HttpClient\HttpClients\Macros
 */
class BrowserMacro
{
    /**
     * @param ChromeBrowser $browser
     * @return void|string|null
     */
    public function __invoke(ChromeBrowser $browser)
    {
        $browser->ensurejQueryIsAvailable();
        $browser->resize(375, 1648);
        $browser->getDriver()->executeScript('$("title").html("書き換え")');
    }
}
