<?php
/**
 * Este archivo es usado por \test\labo86\hapi_core\RequestTest
 */
declare(strict_types=1);

use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi_core\Request;

include_once(__DIR__ . '/../../../../../../vendor/autoload.php');


$server_info = new Request();

try {
    echo $server_info->getParameter('method');
} catch ( Throwable $exception ) {
    echo json_encode(\labo86\exception_with_data\Util::toArray($exception, false));
}