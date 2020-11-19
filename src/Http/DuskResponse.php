<?php


namespace KSuzuki2016\HttpClient\Http;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;

class DuskResponse extends Response
{
    public function crawler()
    {
        return new Crawler($this->body());
    }
}
