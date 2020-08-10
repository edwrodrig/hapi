<?php
declare(strict_types=1);

use labo86\hapi\Controller;
use labo86\hapi_core\Request;
use labo86\hapi_core\Response;
use labo86\hapi_core\ResponseJson;

include_once(__DIR__ . '/../../vendor/autoload.php');

$_ENV['ERROR_LOG_FILENAME'] = '/home/edwin/error_log';


function sum(int $a, int $b) : int {
    return $a + $b;
}

function concat(string $first, string $second) : string {
    return $first . "-" . $second;
}

$controller = new Controller();
$controller->registerFunctionsInFile(__FILE__);

$controller->getServiceMap()
    ->registerService('echo', function(Request $request) : Response {
        return new ResponseJson([
            'message' => 'successful',
            'request_params' => $request->getParameterList()
        ]);
    });

$controller->run();


