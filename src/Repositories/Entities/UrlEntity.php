<?php

    namespace HttpClient\Repositories\Entities;

    use HttpClient\HttpClients\Guzzle;
    use HttpClient\Repositories\Entity;
    use HttpClient\Repositories\Exceptions\UrlGenerationException;
    use HttpClient\Repositories\RouteUrlGenerator;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\App;
    use League\Uri\UriTemplate;

    /**
     * Class UrlEntity
     *
     * @property int network_id
     * @property string|null name
     * @property string|null method
     * @property string|null uri
     * @property string|null group
     * @property string|null action
     * @property string|null url
     *
     * @method int network_id
     * @method string|null name
     * @method string|null method
     * @method string|null uri
     * @method string|null group
     * @method string|null action
     * @method string|null url
     *
     * @package HttpClient\Repositories\Entities
     */
    class UrlEntity extends Entity
    {

        public $defaultParameters = [] ;

        public $source = Guzzle::class ;


        protected $fillable = [
            'network_id','name','method','uri','group','action','url',
        ] ;

        public static function getUrl($parameters = []):string
        {
            try{
                return RouteUrlGenerator::to( new static ,$parameters ) ;
            }catch ( UrlGenerationException $e ){
                return '' ;
            }
        }

        public function getUriTemplate(array $defaultParameters = []):UriTemplate
        {
            return new UriTemplate( $this->getAttribute('uri') , $defaultParameters) ;
        }

        public function defaultParameters(array $defaultParameters = []):array
        {
            return array_merge( $this->defaultParameters , $defaultParameters ) ;
        }

        protected function makeSource($class)
        {
            return App::make($class) ;
        }

        protected function boot(): void
        {
            $this->attributes['method'] = strtoupper($this->attributes['method']??'GET') ;
            parent::boot();
        }
    }
