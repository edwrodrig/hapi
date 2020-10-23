<?php
declare(strict_types=1);

namespace labo86\hapi;

use labo86\exception_with_data\ExceptionForFrontEnd;
use labo86\exception_with_data\MessageMapper;
use Throwable;

class Controller
{
    protected ServiceMap $service_map;

    protected string $error_log_filename = 'php://temp';

    protected MessageMapper $message_mapper;

    public function __construct() {
        $this->service_map = new ServiceMap();
    }

    public function setErrorLogFilename(string $error_log_filename) {
        $this->error_log_filename = $error_log_filename;
    }

    public function getServiceMap() : ServiceMap {
        return $this->service_map;
    }

    public function setMessageMapper(MessageMapper $mapper) {
        $this->message_mapper = $mapper;
    }

    /**
     * @codeCoverageIgnore
     * @return Request
     */
    protected function getRequest() : Request {
        return new Request();
    }
    
    public function handleRequest(Request $request) : Response {
        try {
            $http_method = $request->getHttpMethod();
            if ( $http_method === 'OPTIONS' ) {
                $response = new Response();
                $response->setHttpResponseCode(204);

                return $response;

            } else {

                $method = $request->getStringParameter('method');

                $method_callback = $this->service_map->getService($method);
                return $method_callback($request);
            }

        } catch ( Throwable $throwable ) {

            $exception = ExceptionForFrontEnd::normalize($throwable, $this->message_mapper);
            $data = $exception->toArray();
            $data['t'] = date('Y-m-d H:i:s');
            file_put_contents($this->error_log_filename, json_encode($data, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);


            $response = new ResponseJson($exception->getDataForUser());
            $response->setHttpResponseCode(400);
            return $response;
        }
    }

    /**
     * Casi lo mismo que {@see callRequest()} pero aquí crea una nueva request
     */
    public function run() {
        $request = $this->getRequest();
        $this->handleRequest($request)->send();
    }
}