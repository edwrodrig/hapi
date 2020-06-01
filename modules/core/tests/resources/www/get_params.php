<?php
/**
 * Este archivo es usado por \test\edwrodrig\hapi_core\RequestTest
 */
declare(strict_types=1);

use edwrodrig\hapi_core\Request;

include_once(__DIR__ . '/../../../../../vendor/autoload.php');

$server_info = new Request();

echo json_encode($server_info->getParams(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);