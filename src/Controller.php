<?php
declare(strict_types=1);


namespace edwrodrig\hapi;

use edwrodrig\hapi_core\Request;
use edwrodrig\hapi_core\ResponseJson;
use edwrodrig\hapi_core\ServiceMap;
use Throwable;

class Controller
{
    protected ServiceMap $service_map;

    protected Request $request;

    protected string $error_log_filename;

    public function __construct() {
        $this->service_map = new ServiceMap();
        $this->request = new Request();
        $this->error_log_filename = $this->getEnvVar('ERROR_LOG_FILENAME') ?? 'php://tmp';
    }

    public function setErrorLogFilename(string $path) {
        $this->error_log_filename = $path;
    }

    public function getServiceMap() : ServiceMap {
        return $this->service_map;
    }

    protected function getRequest() : Request {
        return $this->request;
    }

    /**
     * @codeCoverageIgnore
     * @param string $var_name
     * @return string|null
     */
    protected function getEnvVar(string $var_name) : ?string {
        return $_SERVER[$var_name] ?? $_ENV[$var_name] ?? null;
    }

    public function run() {
        try {
            $request = $this->getRequest();
            $method = $request->getMethod();

            $response = $this->service_map->getService($method)($request);
            $response->send();
        }
        catch ( Throwable $throwable ) {

            $log_data = ServiceException::getDataForDeveloper($throwable);
            $log_data['i'] = uniqid();
            file_put_contents($this->error_log_filename, json_encode($log_data, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);
            http_response_code(400);

            $user_data = ServiceException::getDataForUser($throwable);
            $user_data['i'] = $log_data['i'];
            $response = new ResponseJson($user_data);
            $response->send();


        }
    }
}