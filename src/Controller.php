<?php
declare(strict_types=1);


namespace labo86\hapi;

use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi_core\Request;
use labo86\hapi_core\Response;
use labo86\hapi_core\ResponseJson;
use labo86\hapi_core\ServiceMap;
use ReflectionException;
use ReflectionFunction;
use ReflectionType;
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

    public function run() {
        try {
            $request = $this->getRequest();
            $method = $request->getParameter('method');

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

    /**
     * @param string $function_name
     * @throws ReflectionException
     * @throws ExceptionWithData
     */
    public function registerFunction(string $function_name) {
        $reflection_function = new ReflectionFunction($function_name);
        $parameter_info_list = ServiceFunctionReflector::getParameterInfoList($reflection_function);


        $this->getServiceMap()->registerService($function_name, function(Request $request) use ($reflection_function, $parameter_info_list) {

           $parameter_value_list = ServiceFunctionReflector::getParameterValueListFromRequest($request, $parameter_info_list);
           error_log(print_r($parameter_value_list, true));
           $response = $reflection_function->invoke(...$parameter_value_list);

           if ( !$response instanceof Response )
               $response = new ResponseJson($response);

            return $response;
        });

    }

}