# laravel-http-client

LaravelのHttpファサードでヘッドレスブラウザ(Dusk)を使える様に拡張

## インストール

Chromeをサーバーへインストール  

google-chromeとかgoogle-chrome-stableあたりが入ってれば大丈夫  

``` shell script
sudo wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
sudo sh -c 'echo "deb http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list'
sudo apt-get update
sudo apt-get install -y google-chrome-stable
```

ドライバの追加

現状で利用可能なバーションは`70`から`73`のバージョン限定

``` shell script
php artisan dusk:chrome-driver 70
```

## 使い方

基本的な機能は [Laravel HTTPクライアント](https://readouble.com/laravel/8.x/ja/http-client.html) と同じでいくつかの機能追加をしてある

### 設定

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

### 使い方

`manager`クラスからクライアントドライバを呼び出して使う

``` php
<?php

use KSuzuki2016\HttpClient\DriverManager ;

$manager = app(KSuzuki2016\HttpClient\DriverManager::class) ;
$manager->driver('dusk')->get('URL') ;

```

### Httpファサード拡張

`response`に`crawler`を追加

*Symfony\Component\DomCrawler\Crawler*を返します

``` php
<?php
use KSuzuki2016\HttpClient\DriverManager ;

$manager = app(KSuzuki2016\HttpClient\DriverManager::class) ;
$manager->driver('dusk')->get('URL')->crawler() ;
```

### javascript macro

javascriptの実行が可能

`browserCallback`に設定

``` php
<?php
use KSuzuki2016\HttpClient\DriverManager ;

$manager = app(KSuzuki2016\HttpClient\DriverManager::class) ;
$manager->browserCallback( new BrowserMacro )->driver('dusk')->get('URL') ;
```

macro サンプル

戻り値はレスポンスヘッダーの`stacks`に格納されている

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

## Macro Make Command

Macroのひな形を作成

``` shell script
$ php artisan make:http:macro CustomMacro
```

## TODO

今後の予定は`Extensions`でテスト中

1. ~~よく使う設定をクラスにまとめる機能~~
1. ロギング処理
1. ~~stubコマンド~~
1. ドライバのバーションアップ
1. ~~マルチドライバ対応~~

