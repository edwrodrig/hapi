<?php
declare(strict_types=1);

namespace test\edwrodrig\hapi;

use edwrodrig\hapi_core\BuiltInServer;
use PHPUnit\Framework\TestCase;

class ExamplesTest extends TestCase
{

    public function assertJsonStringToArray(array $expected, string $actual, string $message = "") {
        $this->assertJson($actual);
        $this->assertEquals($expected, json_decode($actual, true), $message);
    }

    /**
     * Testeando examples/www/index.php
     */
    public function testExampleWww()
    {
        $server = new BuiltInServer(__DIR__ . '/../examples/www');
        $server->run();

        $response = $server->makeRequest('index.php');
        $this->assertJsonStringToArray([
            'error' => 'Wrong request',
            'try_this' => 'http://localhost:8080/index.php?method=echo'
        ],  $response);

        $response = $server->makeRequest('index.php?method=echo');
        $this->assertJsonStringToArray([
            'message' => 'successful',
            'request_params' => [
                'method' => 'echo'
            ]
        ],  $response);

    }

}