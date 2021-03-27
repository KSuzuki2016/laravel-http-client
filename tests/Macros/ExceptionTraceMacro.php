<?php

namespace Tests\Macros;

use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Browser\Chrome\ChromeBrowser;

/**
 * Class ExceptionTraceMacro
 * @package Tests\Macros
 */
class ExceptionTraceMacro
{

    /**
     * @param ChromeBrowser $browser
     * @return void|string|null
     */
    public function __invoke(ChromeBrowser $browser)
    {
        $browser->ensurejQueryIsAvailable();
        $browser->getDriver()->executeScript('Script Error');
    }
}
