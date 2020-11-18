<?php


    namespace HttpClient;

    use Illuminate\Http\Client\Response ;
    use Symfony\Component\DomCrawler\Crawler;

    class DuskResponse extends Response
    {
        public function crawler()
        {
            return new Crawler( $this->body() ) ;
        }
    }
