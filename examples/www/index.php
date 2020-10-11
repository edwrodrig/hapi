<?php
declare(strict_types=1);

use labo86\hapi\Request;
use labo86\hapi\Response;
use labo86\hapi\ResponseJson;
use labo86\hapi\ServiceMap;

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
    $method = $request->getParameter('method');

    /** Se construye el ServiceMap */
    $services = new ServiceMap();

    /** Y se registra el servicio que creamos anteriormente */
    $services
        ->registerService('echo', function(Request $request) : Response {
            return new ResponseJson([
                'message' => 'successful',
                'request_params' => $request->getParameterList()
            ]);
        })
        ->registerService('date', function(Request $request) : Response {
            $response = new ResponseJson([
                'message' => date('H:i:s'),
                'request_params' => $request->getParameterList()
            ]);
            $response->addHeader('Cache-Control: max-age=10');
            return $response;
        })
        ->registerService('cookie', function(Request $request) : Response {
            $response = new ResponseJson([
                'message' => date('H:i:s'),
                'request_params' => $request->getParameterList()
            ]);
            $response->setCookie('wachulin', 'something');
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
    http_response_code(404);

    $response = new ResponseJson([
        'error' => $e->getMessage(),
        'try_this' => 'http://localhost:8080/index.php?method=echo'
    ]);
    $response->send();

}


