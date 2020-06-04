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

    protected string $error_log_filename = 'php://tmp';

    public function __construct() {
        $this->service_map = new ServiceMap();
    }

    public function setErrorLogFilename(string $path) {
        $this->error_log_filename = $path;
    }

    public function getServiceMap() : ServiceMap {
        return $this->service_map;
    }

    public function run() {
        try {

            $request = new Request();
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