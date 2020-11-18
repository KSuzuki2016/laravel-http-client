# laravel-http-client

# インストール

google-chromeとかgoogle-chrome-stableあたりが入ってれば大丈夫  

## Chromeをサーバーへインストール  

```
sudo wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
sudo sh -c 'echo "deb http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list'
sudo apt-get update
sudo apt-get install -y google-chrome-stable
```

## ドライバの追加

```
php artisan dusk:chrome-driver -all
php artisan dusk:install
```

## ログの設定

エラーログとデバッグログの設定  

logging.php  
```
'clientlog' => [
    'driver' => 'daily',
    'log_max_files' => '10',
    'log_level' => 'debug' ,
    'path' => storage_path('logs/clientlog.log'),
],
```
# Httpファサードを拡張

## メソッド追加
get メソッドが返却したインスタンスに crawler を追加  
Symfony\Component\DomCrawler\Crawler を返します

```
$response->crawler(): Crawler
```

[
Laravel 7.x HTTPクライアント > リクエスト作成](https://readouble.com/laravel/7.x/ja/http-client.html#making-requests)

## Duskメソッド追加

getリクエストの際にヘッドレスブラウザがクライアントになる  

```
use Illuminate\Support\Facades\Http;

Http::dusk();

Http::get(...) ;
```

## UserAgentの指定 

```
$get = Http::withOptions([
    'userAgent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X)'
]
```

その他のオプションはまだやってないので下記で対応  

```
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

```
use Illuminate\Support\Facades\Http;

Http::dusk();

Http::browserCallback( new BrowserMacro );
```

### サンプル

```
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


# RouteResource

TitleResource、TitleListResourceをRouteResource経由で呼び出さないと、```$this->routes->current()``` が設定されないので、直近のrouteか空になって正しい値が取れなくなる

```
use HttpClient\Repositories\RouteResource ;

// Resource取得
app(RouteResource::class)->resource('netflix.title')->findById(123456) ;

// RouteEntityで同じ事をする場合
use HttpCrawler\Netflix\Routes\NetflixTitleRoute ;
// currentRouteをセット
app(RouteResource::class)->route('netflix.title');
app(NetflixTitleRoute::class)->resource()->findById(123456) ;


// HTTPレスポンス取得
app(RouteResource::class)->source('netflix.title',123456) ;

// route取得
app(RouteResource::class)->route('netflix.title') ;

// TitleResource取得
app(RouteResource::class)->title(10)

// network_idとtitleをセットしたResourceを取得
app(RouteResource::class)->getNetworkTitleResource( NetworkTitleModel::first() )

// list resource
app(RouteResource::class)
->resource('netflix.episode.list')  // リソース取得
->title('123456')                   // タイトルIDセット
->setTitleName('タイトル')           // タイトル名セット
->parameters([追加パラメータ])         // リクエスト時に追加するパラメータ $this->listParameters にセットする場合
->resource([追加パラメータ])           // リクエスト時に追加するパラメータ resource の引数で受け取る場合

```

# RouteListCollection

```
use HttpCrawler\RouteListCollection;

// ネットワークでフィルタ
app(RouteListCollection::class)->network(10)

// activeがtrueでフィルタ後にnameでフィルタ
app(RouteListCollection::class)->active()->routeName( 'title.list' )

// XxxxListResourceが返却されるのでPOST
app(RouteListCollection::class)->mapWithResource(function( $resource ) {
    return $resource->post() ;
});

```

## Routeの設定方法

- 設定可能なパラメータは下記

```
protected $attributes = [
    'network_id'=> ネットワークID ,
    'name'      => ドットで連結した文字列 ,
    'method'    => リクエストメソッド ,
    'uri'       => URLテンプレート ,
    'action'    => 未使用 ,
    'source'    => HTTPリクエストクライアント ,
    'url'       => テンプレートから変換したURL ,
    'group'     => 未使用 ,
    'resource'  => 名称変更する場合設定 ,
];

```

各パラメータ初期値  

```
// こっちはリクエストパラメータのデフォルト
public $requestParameters = [
    'num_story[]' => '-1' ,
];

// こっちはテンプレートのデフォルト
protected function booted(): void
{
    parent::booted();
    $this->defaultParameters = [
        'cours' => Carbon::now()->subMonths(3)->format('Ym')
    ];
}

```

## RouteからResourceの呼び出し

- デフォルトではroute名からResourcesディレクトリ内にあるresourceを呼び出す

- 指定する場合はrouteファイルに対象クラスを設定する

```

protected $attributes = [
    'name'      => 'netflix.title.list' ,
    'method'    => 'GET' ,
    'uri'       => 'https://www.netflix.com/jp/browse/genre/6721' ,
    'group'     => 'title' ,
    'resource'  => NetflixTitleListResource::class , // ここに設定する
];

```

# URLからマッチするRouteを返却する

```
app(RouteResource::class)->matchUrl('http://hogehoge~~~')
```

# 開発時用

ファイル名一括変換  

```
rename "s/Netflix/Hulu/;" *.php
rename "s/Netflix/Hulu/;" Resources/*.php
rename "s/Netflix/Hulu/;" Routes/*.php
```

# 確認用

```
use HttpClient\Repositories\RouteResource ;

app(RouteResource::class)->resource('syoboi.title')->findById(3588)->title();
app(RouteResource::class)->resource('d_anime.title')->findById(21598)->title();
app(RouteResource::class)->resource('netflix.title')->findById(81030224)->title();
app(RouteResource::class)->resource('hulu.title')->findById('namiyo')->title();
app(RouteResource::class)->resource('bandai.title')->findById(4418)->title();
app(RouteResource::class)->resource('tmdb.title')->findById(63406)->title();

app(RouteResource::class)->resource('syoboi.title.list')->get();
app(RouteResource::class)->resource('d_anime.title.list')->get();
app(RouteResource::class)->resource('netflix.title.list')->get();
app(RouteResource::class)->resource('hulu.title.list')->get();
app(RouteResource::class)->resource('bandai.title.list')->get();
app(RouteResource::class)->resource('tmdb.title.list')->get();

```
