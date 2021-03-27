<?php


namespace KSuzuki2016\HttpClient\Macros;


use KSuzuki2016\HttpClient\Drivers\ChromeBrowser;

/**
 * Class DocumentHTML
 * @package KSuzuki2016\HttpClient\Macros
 */
class DocumentHTML
{
    /**
     * @var string
     */
    protected $html;

    /**
     * @var string
     */
    protected $selector;

    /**
     * DocumentHTML constructor.
     * @param string $html
     * @param string $selector
     */
    public function __construct(string $html, string $selector = 'html')
    {
        $this->html = $html;
        $this->selector = $selector;
    }

    /**
     * @param ChromeBrowser $browser
     * @return void|string|null
     */
    public function __invoke(ChromeBrowser $browser)
    {
        $browser->ensurejQueryIsAvailable();
        $browser->getDriver()->executeScript(static::script($this->selector, $this->html));
    }

    public static function script(string $selector, string $html): string
    {
        $json = json_encode($html, JSON_THROW_ON_ERROR);
        return <<<SCRIPT
(function($){
$('{$selector}').empty().html({$json}) ;
})($);
SCRIPT;
    }
}
