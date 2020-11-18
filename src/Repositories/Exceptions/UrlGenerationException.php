<?php


    namespace HttpClient\Repositories\Exceptions;


    use Exception;
    use HttpClient\Repositories\Entities\UrlEntity;

    class UrlGenerationException extends Exception
    {
        /**
         * Create a new exception for missing route parameters.
         *
         * @param  UrlEntity  $route
         * @return UrlGenerationException
         */
        public static function forMissingParameters($route)
        {
            return new static("Missing required parameters for [Route: {$route->name}] [URI: {$route->uri}].");
        }

        /**
         * Create a new exception for missing route parameters.
         *
         * @param  UrlEntity  $route
         * @return UrlGenerationException
         */
        public static function routeActionNotDefinedException($action)
        {
            return new static("Route Action {$action} not defined.");
        }

        public static function routeNotDefinedException($name)
        {
            return new static("Route Name [{$name}] not defined.");
        }

        public static function hasNotRouteGroupDefinedException($group)
        {
            return new static("Route Group {$group} not defined.");
        }

        public static function urlNotGenerateException()
        {
            return new static("Route uri not generate.");
        }
    }
