# laravel-http-client

LaravelのHttpファサードを拡張して利用するライブラリ

基本的な機能は[Laravel HTTPクライアント]と同じ、その他いくつかの機能追加

## インストール

``` shell script
$ composer require KSuzuki2016/laravel-http-client
```

### Chromeをサーバーへインストール 

google-chromeとかgoogle-chrome-stableあたりが入ってれば大丈夫  

``` shell script
sudo wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
sudo sh -c 'echo "deb http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list'
sudo apt-get update
sudo apt-get install -y google-chrome-stable
```

### ドライバの追加

現状で利用可能なバーションは`70`から`73`のバージョン限定

``` shell script
php artisan dusk:chrome-driver 70
```

## 設定

``` php
<?php

return [

    'crawler'   => env('HTTP_RESPONSE_CRAWLER', \Symfony\Component\DomCrawler\Crawler::class ) ,

    /*
    |--------------------------------------------------------------------------
    | Http Request Driver
    |--------------------------------------------------------------------------
    | dusk is alias for dusk-chrome
    |
    | Drivers: "guzzle", "dusk" , "dusk-chrome"
    |
    */
    'default' => env('HTTP_CLIENT_DRIVER', 'guzzle') ,

    'http_facade_overwrite' => env('HTTP_FACADE_OVERWRITE', false ) ,

];
```

## 使い方

`manager`クラスからクライアントドライバを呼び出して使うか`http-client`を呼び出す

`HTTP_FACADE_OVERWRITE`に`true`が設定されていれば**Httpファサード**を上書きする

``` php
<?php

use KSuzuki2016\HttpClient\DriverManager ;

$manager = app(KSuzuki2016\HttpClient\DriverManager::class) ;
$manager->driver('dusk')->get('URL') ;

// 又は

$manager = app('http-client') ;
$manager->get('URL') ;
```

## Response拡張

`response`に`crawler`と`stacks`を追加

`crawler`は*Symfony\Component\DomCrawler\Crawler*を返す

`stacks`は*配列*を返す

``` php
<?php
use KSuzuki2016\HttpClient\DriverManager ;

$manager = app(KSuzuki2016\HttpClient\DriverManager::class) ;
$manager->driver('dusk')->get('URL')->crawler() ;
$manager->driver('dusk')->get('URL')->stacks() ;
```

## javascript browser macro

javascriptの実行が可能

`browserCallback`に設定

``` php
<?php
use KSuzuki2016\HttpClient\DriverManager ;

$manager = app(KSuzuki2016\HttpClient\DriverManager::class) ;
$manager->browserCallback( new BrowserMacro )->driver('dusk')->get('URL') ;
```

### 例

戻り値はレスポンスヘッダーの`stacks`に格納される

``` php
<?php
namespace HttpClient\Macros;

use KSuzuki2016\HttpClient\Drivers\ChromeBrowser;

class TestMacro
{
    /**
     * @param ChromeBrowser $browser
     * @return void|string|null
     */
    public function __invoke(ChromeBrowser $browser)
    {
        // Browser Macro
    }
}
```

## Response Observer

レスポンスに対して処理を行う

`responseObserver`に設定

``` php
<?php
use KSuzuki2016\HttpClient\DriverManager ;

$manager = app(KSuzuki2016\HttpClient\DriverManager::class) ;
$manager->responseObserver( new ResponseLogObserver )
        ->responseObserver( new ResponseObserver )
        ->driver('dusk')->get('URL') ;
```

### 実装方法

`ResponseObserverInterface`を実装して作成するか`ResponseObserver`を継承する

連続したObserverの処理を止めたい場合は`getObservation`から`false`を返却する

`successful`と`failed`のから返却された`HttpClientResponse`で次の処理を行う(返却しなければオリジナルの値を使う)

### 例

``` php
<?php
namespace KSuzuki2016\HttpClient\Logging;

use KSuzuki2016\HttpClient\Contracts\ResponseObserver;
use KSuzuki2016\HttpClient\Http\Client\HttpClientResponse;

class ResponseLogObserver extends ResponseObserver
{
    public function successful(HttpClientResponse $response)
    {
        // 処理を書く
    }

    public function failed(HttpClientResponse $response)
    {
        // ResponseObserverを継承していれば
        // 以降の処理を止める場合にbreakObservationを呼び出して停止可能
        $this->breakObservation();
    }
}
```

## Make Command

stubからひな形を作成できます

### Browser Macro

Browser Macroのひな形を作成

``` shell script
$ php artisan make:http:macro CustomMacro
```

### Response Observer

Response Observerのひな形を作成

``` shell script
$ php artisan make:http:Observer CustomObserver
```

[Laravel HTTPクライアント]:https://readouble.com/laravel/8.x/ja/http-client.html
