<?php
declare(strict_types=1);

namespace test\labo86\hapi_core;

use labo86\hapi_core\ResponseJson;
use PHPUnit\Framework\TestCase;

class ResponseJsonTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testSend()
    {
        $originalData = ['a' => 1, 'b' => 2];
        $response = new ResponseJson($originalData);
        ob_start();
        $response->send();
        $json_data = ob_get_clean();
        $recoveredData = json_decode($json_data, true);
        $this->assertEquals($originalData, $recoveredData);
    }
}
