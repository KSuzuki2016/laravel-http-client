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

## 機能

### Httpファサード拡張

`response`に`crawler`を追加

*Symfony\Component\DomCrawler\Crawler*を返します

``` php
$response->crawler(): Crawler
```

[
Laravel 7.x HTTPクライアント > リクエスト作成](https://readouble.com/laravel/7.x/ja/http-client.html#making-requests)

### Duskメソッド追加

getリクエストの際にヘッドレスブラウザがクライアントになる  

``` php
use Illuminate\Support\Facades\Http;

Http::dusk([script macro]);

Http::get(...) ;
```

### javascript macro

**Duskモード利用時**にjavascriptの実行が可能

`dusk`宣言時もしくは`browserCallback`に設定

``` php
use Illuminate\Support\Facades\Http;

Http::dusk(new BrowserMacro);
// どちらか
Http::browserCallback( new BrowserMacro );
```

macro サンプル

戻り値はレスポンスヘッダーの`stacks`に格納されている

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
        $browser->getDriver()->executeScript('$("h1").html("Rewrite Head")') ;
    }
}
```

## TODO

今後の予定は`Extensions`でテスト中

1. よく使う設定をクラスにまとめる機能
1. ロギング処理
1. stubコマンド
1. ドライバのバーションアップ
