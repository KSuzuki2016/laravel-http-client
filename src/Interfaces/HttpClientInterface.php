<?php


    namespace HttpClient\Interfaces;


    use Illuminate\Http\Client\Response;

    interface HttpClientInterface
    {
        public function request( $method ,$url, $parameters = []):Response ;

        public function send( $method ,$url, $parameters = []):Response ;

    }
