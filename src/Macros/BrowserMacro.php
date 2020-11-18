<?php


    namespace HttpClient\Macros;


    use HttpClient\WebDriver\ChromeBrowser;

    class BrowserMacro
    {
        /**
         * @param ChromeBrowser $browser
         * @return void|string|null
         */
        public function __invoke(ChromeBrowser $browser)
        {
            $browser->ensurejQueryIsAvailable() ;
            $browser->resize(375,1648) ;
            $browser->getDriver()->executeScript('$("h1").html("書き換え")') ;
        }
    }
