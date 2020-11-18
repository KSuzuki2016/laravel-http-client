<?php


    namespace HttpClient\HttpClients ;

    use Http ;
    use HttpClient\HttpClient;
    use Illuminate\Http\Client\Response;

    class GuzzleXMLHttp extends HttpClient
    {
        public function send($method ,$url, $parameters = null):Response
        {
            return Http::withHeaders([
                'x-requested-with' => 'XMLHttpRequest',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36',
            ])->{$method}($method,$url,$parameters?$parameters:null) ;
        }

    }
