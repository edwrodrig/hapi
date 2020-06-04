<?php
declare(strict_types=1);

use edwrodrig\hapi\Controller;
use edwrodrig\hapi_core\Request;
use edwrodrig\hapi_core\Response;
use edwrodrig\hapi_core\ResponseJson;

include_once(__DIR__ . '/../../vendor/autoload.php');


$controller = new Controller();
$controller->setErrorLogFilename('/home/edwin/error_log');
$controller->getServiceMap()
    ->registerService('echo', function(Request $request) : Response {
        return new ResponseJson([
            'message' => 'successful',
            'request_params' => $request->getParams()
        ]);
    });

$controller->run();


