<?php
declare(strict_types=1);

use labo86\hapi\Controller;
use labo86\hapi_core\Request;
use labo86\hapi_core\Response;
use labo86\hapi_core\ResponseJson;

include_once(__DIR__ . '/../../vendor/autoload.php');


$controller = new Controller();
$controller->getServiceMap()
    ->registerService('echo', function(Request $request) : Response {
        return new ResponseJson([
            'message' => 'successful',
            'request_params' => $request->getParams()
        ]);
    });

$controller->run();


