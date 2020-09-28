<?php
/**
 * Este archivo es usado por \test\labo86\hapi\RequestTest
 */
declare(strict_types=1);

use labo86\hapi\Request;

include_once(__DIR__ . '/../../../../vendor/autoload.php');

$server_info = new Request();

echo json_encode($server_info->getParameterList(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);