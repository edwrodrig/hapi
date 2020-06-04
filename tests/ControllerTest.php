<?php
declare(strict_types=1);

namespace test\edwrodrig\hapi;

use edwrodrig\hapi\Controller;
use edwrodrig\hapi_core\BuiltInServer;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{

    public function assertResponseToArray(array $expected, string $actual, string $message = "") {
        $this->assertJson($actual);
        $actual = json_decode($actual, true);
        unset($actual['i']);
        $this->assertEquals($expected, $actual, $message);
    }

    /**
     * Testeando examples/www2/index.php
     */
    public function testExampleWww()
    {
        $server = new BuiltInServer(__DIR__ . '/../examples/www2');
        $server->run();

        $response = $server->makeRequest('index.php');
        $this->assertResponseToArray([
            'm' => 'internal error',
            'd' => []
        ],  $response);

        $response = $server->makeRequest('index.php?method=echo');
        $this->assertResponseToArray([
            'message' => 'successful',
            'request_params' => [
                'method' => 'echo'
            ]
        ],  $response);

    }


}
