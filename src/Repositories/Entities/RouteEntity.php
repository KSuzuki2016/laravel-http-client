<?php


    namespace HttpClient\Repositories\Entities;


    use HttpClient\HttpClients\Guzzle;
    use HttpClient\Repositories\Exceptions\RouteResourceException;
    use HttpClient\Repositories\Exceptions\UrlGenerationException;
    use Illuminate\Support\Str;

    /**
     * Class RouteEntity
     *
     * @property string|null method
     * @property string|null action
     * @property string|null source
     *
     * @method string|null action
     *
     * @package HttpClient\Repositories\Entities
     */
    class RouteEntity extends UrlEntity
    {

        public $requestParameters = [
        ] ;

        public $source = Guzzle::class ;

        protected $fillable = [
            'network_id','name','method', 'uri','action','source','group','url','resource',
        ] ;

        public function source(array $parameters = [])
        {
            $parameters = array_merge( $this->requestParameters ,$parameters ) ;
            if( isset( $this->attributes['url'] ) )
            {
                return $this->makeSource($this->attributes['source'])
                            ->__invoke( $this->get('method') , $this->get('url') , $parameters ) ;
            }
            throw UrlGenerationException::urlNotGenerateException();
        }

        /**
         */
        public function resource()
        {
            if( class_exists($this->attributes['resource']) ) {
                return resolve( $this->attributes['resource'] ) ;
            }
            throw new RouteResourceException('resource class not found [' . $this->resource . ']') ;
        }

        public function method()
        {
            return strtolower($this->getAttribute('method','')) ;
        }

        public function generateUrl( $parameters = [] )
        {
            $this->set( 'url' , static::getUrl($parameters) ) ;
            return $this ;
        }

        protected function booted(): void
        {
            parent::offsetSet('source', $this->attributes['source']??$this->source ) ;
            if(! isset($this->attributes['resource']) && class_exists($resource = $this->buildResource( $this->get('name') ))){
                parent::offsetSet('resource', $resource ) ;
            }
        }

        public function buildResource( $class_name ): string
        {
            $class_name = Str::studly(str_replace('.','_',$class_name)) ;
            return Str::beforeLast( get_class($this) ,'\\Routes') . '\\Resources\\' . $class_name . 'Resource' ;
        }

    }
