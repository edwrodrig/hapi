<?php
declare(strict_types=1);

namespace labo86\hapi;

use labo86\exception_with_data\ExceptionForFrontEnd;
use Throwable;

class Controller
{
    protected ServiceMap $service_map;

    protected string $error_log_filename;

    public function __construct() {
        $this->service_map = new ServiceMap();
        $this->error_log_filename = $this->getEnvVar('ERROR_LOG_FILENAME') ?? 'php://temp';
    }

    public function getServiceMap() : ServiceMap {
        return $this->service_map;
    }

    /**
     * @codeCoverageIgnore
     * @return Request
     */
    protected function getRequest() : Request {
        return new Request();
    }

    /**
     * @param string $var_name
     * @return string|null
     */
    protected function getEnvVar(string $var_name) : ?string {
        return $_SERVER[$var_name] ?? $_ENV[$var_name] ?? null;
    }

    
    public function handleRequest(Request $request) : Response {
        try {
            $method = $request->getStringParameter('method');

            return $this->service_map->getService($method)($request);


        } catch ( Throwable $throwable ) {

            $exception = ExceptionForFrontEnd::normalize($throwable);

            file_put_contents($this->error_log_filename, json_encode($exception->toArray(), JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);

            return new ResponseJson($exception->getDataForUser());
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