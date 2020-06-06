<?php
/**
 * Este archivo es usado por \test\labo86\hapi_core\RequestTest
 */
declare(strict_types=1);

use labo86\hapi_core\Request;

include_once(__DIR__ . '/../../../../../../vendor/autoload.php');


$server_info = new Request();

echo $server_info->getMethod();