<?php
/**
 * Este archivo es usado por \test\labo86\hapi_core\RequestTest
 */
declare(strict_types=1);

use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi_core\Request;

include_once(__DIR__ . '/../../../../../../vendor/autoload.php');


$server_info = new Request();

$response = [];

try {
    $response['file'] = $server_info->getFileParameter('file');
} catch ( ExceptionWithData $exception ) {
    $response['file'] = [ 'message' => $exception->getMessage(), 'data' => $exception->getData()];
}

try {
    $response['file_list'] =  $server_info->getFileListParameter('file_list');
} catch ( ExceptionWithData $exception ) {
    $response['file_list'] = [ 'message' => $exception->getMessage(), 'data' => $exception->getData()];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);