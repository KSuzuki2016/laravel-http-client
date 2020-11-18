<?php

    namespace HttpClient\Repositories ;

    use Illuminate\Support\Arr;
    use Illuminate\Support\Fluent;
    use Exception ;
    use Illuminate\Support\Str;
    use Throwable ;

    /**
     * Class Entity
     * @package AnilogMaster\Entities
     *
     *
     */
    abstract class Entity extends Fluent
    {
        protected $immutable = false ;

        protected $fillable = [
        ];

        public function __construct( $attributes = [] )
        {
            $this->boot();
            if( filled( $this->fillable ) ) {
                $attributes = Arr::only( $attributes , $this->fillable ) ;
            }
            parent::__construct( $attributes ) ;
            $this->booted();
        }

        public function getAttribute($key,$default=null)
        {
            return parent::get($key,$default);
        }

        /**
         * @throws Throwable
         */
        public function offsetSet($offset, $value)
        {
            if( $this->immutable ) {
                throw  new Exception( get_class($this) . ' is Immutable Entity' ) ;
            }
            if(! (in_array($offset,$this->fillable) || blank($this->fillable)) ) {
                throw  new Exception( $offset . ' is non-existing property' ) ;
            }
            parent::offsetSet($offset, $value) ;
        }

        public function get($key, $default = null)
        {
            if ( in_array($key,$this->fillable) || blank($this->fillable) )
            {
                if( method_exists($this,$key) ) {
                    return $this->{$key}() ;
                }
                return parent::get($key, $default);
            }
            return null ;
        }

         public function __call($method, $parameters)
         {
             if ( in_array($method,$this->fillable) || blank($this->fillable) ){
                 return $this->attributes[$method]??null ;
             }
             $this->attributes[$method] = count($parameters) > 0 ? $parameters[0] : true;
             return $this;
         }

        public function __get($key)
        {
            return parent::__get($key);
        }

        public function __set($key, $value)
        {
            parent::__set($key, $value);
        }

        public function set( $key, $value )
        {
            try {
                $this->offsetSet($key, $value);
            } catch (Exception | Throwable $e) {
                (function () use ($e) { throw new Exception( $e->getMessage() ); })();
            }
        }
        public function __serialize(): array
        {
            return $this->toArray() ;
        }

        public function __unserialize(array $data): void
        {
            $this->__construct($data) ;
        }

        public function __debugInfo() {
            return $this->attributes ;
        }

        protected function boot():void
        {

        }
        protected function booted():void
        {
            $fillable_attributes = array_fill_keys($this->fillable,null) ;
            $this->attributes = array_merge( $fillable_attributes , $this->attributes );
        }


    }
