<?php


    namespace HttpClient\HttpClients ;

    use Http ;
    use HttpClient\HttpClient;
    use Illuminate\Http\Client\Response;

    class GuzzleForm extends HttpClient
    {
        public function send( $method ,$url, $parameters = [] ):Response
        {
            return Http::asForm()->{$method}($url,$parameters) ;
        }
    }
