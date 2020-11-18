<?php


    namespace HttpClient\HttpClients ;

    use Http ;
    use HttpClient\HttpClient;
    use Illuminate\Http\Client\Response;

    /**
     * GETとHEADの場合$parametersに値が入ってると
     * $urlで指定されているクエリパラメータを削除して$parametersを優先してリクエストするので
     * []が入っていた場合にはクエリ文字列なしで送信する事になる
     */
    class Guzzle extends HttpClient
    {
        public function send($method ,$url, $parameters = null):Response
        {
            return Http::$method($url,$parameters?$parameters:null) ;
        }

    }
