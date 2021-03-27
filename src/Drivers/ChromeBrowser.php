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
    /**
     * @param $url
     * @return null|string
     */
    public function get($url): ?string
    {
        $this->visit($url);
        return $this->getDriver()->getPageSource();
    }

    /**
     * @return null|string
     */
    public function getBody(): ?string
    {
        return $this->getDriver()->getPageSource();
    }

    /**
     * @return null|string
     */
    public function screen(): ?string
    {
        $this->screenshot('');
        return $this->getDriver()->getPageSource();
    }

    public function ensurejQueryIsAvailable(): void
    {
        $this->getBrowser()->ensurejQueryIsAvailable();
    }

}
