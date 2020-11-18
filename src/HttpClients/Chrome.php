<?php


    namespace HttpClient\HttpClients ;

    use Http ;
    use HttpClient\HttpClient;
    use HttpClient\Macros\BrowserMacro;
    use Illuminate\Http\Client\Response;
    use Exception ;

    class Chrome extends HttpClient
    {
        public function send($method ,$url, $parameters = []):Response
        {
            if( $method === 'get' ){
                Http::dusk(new BrowserMacro) ;
                return Http::get($url) ;
            }
            throw new Exception('this client GET method only ') ;
        }

    }
