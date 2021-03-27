<?php


namespace KSuzuki2016\HttpClient\HttpClients;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use KSuzuki2016\HttpClient\Contracts\HttpClient;

/**
 * GETとHEADの場合$parametersに値が入ってると
 * $urlで指定されているクエリパラメータを削除して$parametersを優先してリクエストするので
 * []が入っていた場合にはクエリ文字列なしで送信する事になる
 */
class Guzzle extends HttpClient
{
    /**
     * @param $method
     * @param $url
     * @param null $parameters
     * @return Response
     */
    public function send($method, $url, $parameters = null): Response
    {
        return Http::$method($url, $parameters ?: null);
    }

}
