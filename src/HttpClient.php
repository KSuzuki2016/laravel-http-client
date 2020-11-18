<?php


    namespace HttpClient ;


    use HttpClient\Interfaces\HttpClientInterface;
    use HttpClient\Logging\HttpClientLogger;
    use Illuminate\Http\Client\Response;

    abstract class HttpClient implements HttpClientInterface
    {
        protected $logger ;

        public function __construct()
        {
            $this->logger = resolve(HttpClientLogger::class ) ;
        }

        public function __invoke( $method ,$url, $parameters = []):Response
        {
            return $this->request($method ,$url, $parameters) ;
        }

        public function request( $method ,$url, $parameters = []):Response
        {
            return tap( $this->send( $method ,$url, $parameters ) , $this->logger->logging($method ,$url, $parameters) ) ;
        }

        abstract function send($method ,$url, $parameters = []):Response ;

    }
