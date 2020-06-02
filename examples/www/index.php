<?php
declare(strict_types=1);

use edwrodrig\hapi_core\Request;
use edwrodrig\hapi_core\Response;
use edwrodrig\hapi_core\ResponseJson;
use edwrodrig\hapi_core\ServicesMap;

include_once(__DIR__ . '/../../vendor/autoload.php');

try {
    /**
     * Esta sección procesa el request que viene del servidor para que pueda ser usada por el servicio
     * En la mayoría de los casos esto puede ir oculto y hecho por alguna función utilitaria.
     */
    $request = new Request();

    /**
     * Este no es el método http sino un parámetro para saber que servicio utilizar.
     */
    $method = $request->getMethod();

    /** Se construye el ServiceMap */
    $services = new ServicesMap();

    /** Y se registra el servicio que creamos anteriormente */
    $services
        ->registerService('echo', function(Request $request) : Response {
            $response = new ResponseJson();
            $response->data = [
                'message' => 'successful',
                'request_params' => $request->getParams()
            ];
            return $response;
        });


    /** Se obtiene el servicio desde el service map */
    $response = $services->getService($method)($request);

    /** La respuesta se envía */
    $response->send();
}
/**
 * En caso de excepción se puede procesar errores.
 */
catch ( Throwable $e ) {
    header('Content-Type: application/json;charset=utf-8');
    http_response_code(404);

    echo json_encode([
        'error' => $e->getMessage(),
        'try_this' => 'http://localhost:8080/index.php?method=echo'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}


