<?php


    namespace HttpClient\Repositories\Exceptions;


    use Exception;
    use HttpClient\Repositories\Entities\UrlEntity;
    use Throwable;
use ArrayAccess ;

    class ResourceCrawlerException extends Exception
    {
        const invalidResourceItem = 2 ;

        /**
         * @param  $item
         * @return ResourceCrawlerException
         */
        public static function invalidResourceItemException()
        {
            // if(is_array($item) || $item instanceof ArrayAccess ) $item = json_encode( $item,JSON_UNESCAPED_UNICODE);
            return new static("invalid Resource Item" , static::invalidResourceItem );
        }

    }
