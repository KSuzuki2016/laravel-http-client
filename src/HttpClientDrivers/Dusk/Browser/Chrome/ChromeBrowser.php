<?php


namespace KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Browser\Chrome;

use KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Browser\Contracts\DuskBrowser;
use Laravel\Dusk\Browser;

/**
 * Class ChromeBrowser
 *
 * @mixin Browser
 * @package KSuzuki2016\HttpClient\HttpClientDrivers\Dusk\Browser\Chrome
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
