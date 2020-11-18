<?php


    namespace HttpClient\WebDriver ;

    use duncan3dc\Laravel\Dusk;
    use Laravel\Dusk\Browser;

    /**
     * Class ChromeBrowser
     *
     * @mixin Browser
     * @package HttpClient\WebDriver
     */
    class ChromeBrowser extends Dusk
    {
        public function get($url)
        {
            $this->visit($url) ;
            return $this->getDriver()->getPageSource() ;
        }

        public function getBody()
        {
            return $this->getDriver()->getPageSource() ;
        }

        public function screen()
        {
            $this->screenshot('') ;
            return $this->getDriver()->getPageSource() ;
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
