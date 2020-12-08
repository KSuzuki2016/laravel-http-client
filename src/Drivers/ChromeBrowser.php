<?php


namespace KSuzuki2016\HttpClient\Drivers;

use KSuzuki2016\HttpClient\Contracts\DuskBrowser;
use Laravel\Dusk\Browser;

/**
 * Class ChromeBrowser
 *
 * @mixin Browser
 * @package HttpClient\WebDriver
 */
class ChromeBrowser extends DuskBrowser
{
    public function get($url)
    {
        $this->visit($url);
        return $this->getDriver()->getPageSource();
    }

    public function getBody()
    {
        return $this->getDriver()->getPageSource();
    }

    public function screen()
    {
        $this->screenshot('');
        return $this->getDriver()->getPageSource();
    }

    public function ensurejQueryIsAvailable()
    {
        $this->getBrowser()->ensurejQueryIsAvailable();
    }

    public function __destruct()
    {
        parent::__destruct();
    }
}
