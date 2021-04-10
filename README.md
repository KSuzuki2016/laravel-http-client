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

    'binPath' => env('HTTP_CLIENT_CHROME_PATH') ,

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

### chromedriver

`HTTP_CLIENT_CHROME_PATH`に設定したディレクトリ内のドライバを利用

``` dotenv
HTTP_CLIENT_CHROME_PATH="/vagrant/vendor/ksuzuki2016/laravel-http-client/bin"
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

2021.3.27 `schema`を追加

`<script type="application/ld+json">...</script>`のデータを取得して*Collection*として返す

``` php
<?php
use KSuzuki2016\HttpClient\DriverManager ;

$manager = app(KSuzuki2016\HttpClient\DriverManager::class) ;
$manager->get('URL')->schema() ;
```

## javascript browser macro

javascriptの実行が可能

`browserCallback`に設定

``` php
<?php
use KSuzuki2016\HttpClient\DriverManager ;

$manager = app(KSuzuki2016\HttpClient\DriverManager::class) ;
$manager->driver('dusk')->browserCallback( new BrowserMacro )->get('URL') ;
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

## スクリプトエラー

マクロ実行中に発生したエラーログはレスポンスヘッダーの`errors`に格納されます

``` php
$response->header('errors');
/*
unknown error: Runtime.evaluate threw exception: SyntaxError: Unexpected identifier
  (Session info: headless chrome=87.0.4280.66)
  (Driver info: chromedriver=70.0.3538.97 (d035916fe243477005bc95fe2a5778b8f20b6ae1),platform=Linux 4.15.0-96-generic x86_64) screenshot path to screen
*/
```

## Response Observer

レスポンスに対して処理を行う

`responseObserver`に設定

``` php
<?php
use KSuzuki2016\HttpClient\DriverManager ;

$manager = app(KSuzuki2016\HttpClient\DriverManager::class) ;
$manager->driver('dusk')
        ->responseObserver( new ResponseLogObserver )
        ->responseObserver( new ResponseObserver )
        ->get('URL') ;
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

### HttpClientResponseの値を操作する

**ResponseObserver**等で値の変更が可能

#### setStacks( array $stacks ):self

シンプルな例

``` php
public function successful(HttpClientResponse $response)
{
    $stacks = array_merge( $response->stacks() , $response->json() ) ;
    $response->setStacks( $stacks ) ;
}
```

#### setJson( $key , $value = null ):self

値を変更して次の処理へ渡す場合`return`で返却

``` php
public function successful(HttpClientResponse $response)
{
    return $response->setJson( 'key' , 'new value' ) ;
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

## Http Client Request

パラメーターを設定した`PendingRequest`を呼び出す為のクラス

### user agentを設定したクライアントの例

``` php
<?php
namespace App\HttpClients ;

use KSuzuki2016\HttpClient\Contracts\HttpClientRequest;

class MobileClient extends HttpClientRequest
{
    /**
     * observer brawserCallback の設定等を行う場合
     * プロパティでも設定可能
     * @property HttpClientFactory app
     * 
     * サービスプロバイダの bindings の様な設定も可能
     * @property array observers
     * @property array macros
    */

    protected $headers = [
        'user-agent' => 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.91 Mobile Safari/537.36'
    ] ;
}
```

### 利用方法

``` php
$client = new MobileClient ;
$client->get('http://...') ;

// ResponseObserver BrowserMacro の利用
$client->observe( ResponseObserver )->macro( BrowserMacro )->get('http://...') ;

// デバッグを行う場合
$client->debug()->get('http://...') ;
```

[Laravel HTTPクライアント]:https://readouble.com/laravel/8.x/ja/http-client.html
