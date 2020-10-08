<?php
declare(strict_types=1);

namespace test\labo86\hapi\local;

use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi\ErrMsg;
use labo86\hapi\testing\BuiltInServer;
use PHPUnit\Framework\TestCase;

class ExamplesTest extends TestCase
{

    public function assertJsonStringToArray(array $expected, string $actual, string $message = "") {
        $this->assertJson($actual);
        $this->assertEquals($expected, json_decode($actual, true), $message);
    }

    /**
     * Testeando examples/www/index.php
     * @throws ExceptionWithData
     */
    public function testExampleWww()
    {
        $server = new BuiltInServer(__DIR__ . '/../../examples/www');
        $server->run();

        $response = $server->makeRequest('index.php');
        $this->assertJsonStringToArray([
            'error' => ErrMsg::REQUEST_DOES_NOT_HAVE_PARAMETER,
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
