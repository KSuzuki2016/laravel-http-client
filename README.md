# laravel-http-client

# インストール

google-chromeとかgoogle-chrome-stableあたりが入ってれば大丈夫  

## Chromeをサーバーへインストール  

``` php
sudo wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
sudo sh -c 'echo "deb http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list'
sudo apt-get update
sudo apt-get install -y google-chrome-stable
```

## ドライバの追加

``` php
php artisan dusk:chrome-driver -all
php artisan dusk:install
```

## ログの設定

エラーログとデバッグログの設定  

logging.php  

``` php
'http-client-log' => [
    'driver' => 'daily',
    'log_max_files' => '10',
    'log_level' => 'debug' ,
    'path' => storage_path('logs/http-client-log.log'),
],
```
# Httpファサードを拡張

## メソッド追加
get メソッドが返却したインスタンスに crawler を追加  
Symfony\Component\DomCrawler\Crawler を返します

``` php
$response->crawler(): Crawler
```

[
Laravel 7.x HTTPクライアント > リクエスト作成](https://readouble.com/laravel/7.x/ja/http-client.html#making-requests)

## Duskメソッド追加

getリクエストの際にヘッドレスブラウザがクライアントになる  

``` php
use Illuminate\Support\Facades\Http;

Http::dusk();

Http::get(...) ;
```

## UserAgentの指定 

``` php
$get = Http::withOptions([
    'userAgent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X)'
]
```

その他のオプションはまだやってないので下記で対応  

``` php
namespace HttpClient;

class HttpDusk
{
    public function createBrowser($url):void
    {
        $chromeOptions = new ChromeOptions ;
        // ,'--window-size=375,667'
        $chromeOptions->addArguments(['--headless','--disable-gpu','--lang=ja_JP']) ;
        $mobileEmulation = Arr::only( $this->options , ['deviceMetrics'] ) ;
        $mobileEmulation['userAgent'] = Arr::get( $this->options , 'userAgent' , head( (array)$this->request->header('User-Agent') ) ) ;
        $chromeOptions->setExperimentalOption('mobileEmulation', $mobileEmulation ) ;
        $driver = new Driver( $chromeOptions ) ;
        $this->browser = new ChromeBrowser( $driver ) ;
        $this->browser->visit($url) ;
    }
}
```

この辺を触ったらいい  

## javascriptの設定

browserCallbackに設定  

``` php
use Illuminate\Support\Facades\Http;

Http::dusk();

Http::browserCallback( new BrowserMacro );
```

### サンプル

``` php
<?php
namespace HttpClient\Macros;

use HttpClient\WebDriver\ChromeBrowser;

class BrowserMacro
{
    /**
     * @param ChromeBrowser $browser
     * @return void|string|null
     */
    public function __invoke(ChromeBrowser $browser)
    {
        $browser->ensurejQueryIsAvailable() ;
        $browser->resize(375,1648) ;
        $browser->getDriver()->executeScript('$("h1").html("書き換え")') ;
    }
}
```

### レスポンスの取り方

- レスポンスヘッダー内のstacksにスクリプトから返却した文字が入っている


