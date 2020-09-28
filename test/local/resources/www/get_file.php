<?php
/**
 * Este archivo es usado por \test\labo86\hapi\RequestTest
 */
declare(strict_types=1);

use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi\Request;

include_once(__DIR__ . '/../../../../vendor/autoload.php');


$server_info = new Request();

$response = [];

try {
    $response['file'] = $server_info->getFileParameter('file')->getData();
} catch ( Throwable $exception ) {
    $response['file'] = \labo86\exception_with_data\Util::toArray($exception, false);
}

try {
    foreach ( $server_info->getFileListParameter('file_list')  as $file )
        $response['file_list'][] = $file->getData();
} catch ( ExceptionWithData $exception ) {
    $response['file_list'] = \labo86\exception_with_data\Util::toArray($exception, false);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);