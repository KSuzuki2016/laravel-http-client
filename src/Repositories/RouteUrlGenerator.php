<?php


    namespace HttpClient\Repositories;


    use HttpClient\Repositories\Entities\UrlEntity;
    use HttpClient\Repositories\Exceptions\UrlGenerationException;
    use Illuminate\Contracts\Routing\UrlRoutable;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Contracts\Support\Jsonable;
    use Illuminate\Support\Arr;
    use League\Uri\Parser\QueryString ;
    use ArrayAccess ;

    /**
     * Class RouteUrlGenerator
     * @package HttpClient\Repositories
     */
    class RouteUrlGenerator
    {
        public static function to( UrlEntity $route , $parameters = [] )
        {
            return (new static)->generate( $route , $parameters ) ;
        }

        public function generate( UrlEntity $route , $parameters = [] )
        {

            $parameters     = static::formatParameters($parameters);
            $uriTemplate    = $route->getUriTemplate() ;
            $variableNames  = $uriTemplate->getVariableNames() ;
            $parameters     = $this->bindParameters($variableNames,$parameters) ;

            /**
             * UrlEntityに設定してあるdefaultParametersを$parametersでmergeして取得
             * parametersを一次元配列にする
             */
            $parameters     = $this->flatParameters( $route->defaultParameters($parameters) ) ;
            /**
             * URLテンプレートを展開する
             */
            $uriTemplate    = $uriTemplate->expand($parameters) ;
            /**
             * 必要なパラメータがなかった場合エラー
             */
            if( filled($variableNames) && ! Arr::has($parameters,$variableNames) ){
                throw UrlGenerationException::forMissingParameters( $route );
            }
            /**
             * 展開されたURLからqueryを連想配列で取得
             * queryに含まれているキーを$parametersから除外して
             * $uriTemplateにQueryとしてセット
             */
            $query          = QueryString::extract($uriTemplate->getQuery()) ;
            $exceptKeys     = array_merge( array_keys($query) , $variableNames ) ;
            $query          = array_merge( $query , Arr::except($parameters,$exceptKeys) );
            $uriTemplate    = $uriTemplate->withQuery( http_build_query($query) ) ;
            return rtrim( (string) $uriTemplate , '?' ) ;
        }

        /**
         * $parametersが連想配列でない場合
         * $variableNamesのキーを持つ連想配列に変換
         *
         * @param array $variableNames
         * @param array $parameters
         * @return array
         */
        protected function bindParameters(array $variableNames, array $parameters):array
        {
            if(! Arr::isAssoc($parameters) && count($variableNames) <= count($parameters) ){
                return array_combine( $variableNames, (array) array_slice( $parameters ,0, count($variableNames) ) ) ;
            }
            return $parameters ;
        }

        public static function formatParameters($parameters):array
        {
            $parameters = Arr::wrap($parameters);
            foreach ($parameters as $key => $parameter) {
                if ($parameter instanceof UrlRoutable) {
                    $parameters[$key] = $parameter->getRouteKey();
                }
            }
            return $parameters;
        }

        protected function flatParameters(array $parameters):array
        {
            foreach ($parameters as $key => $parameter) {
                if( $parameter instanceof Jsonable ){
                    $parameters[$key] = $parameter->toJson() ;
                }else
                if( $parameter instanceOf Arrayable ){
                    $parameters[$key] = json_encode( $parameter->toArray() );
                }else
                if( $parameter instanceof ArrayAccess || is_array($parameter)) {
                    $parameters[$key] = json_encode($parameter);
                }
            }
            return $parameters;
        }
    }
