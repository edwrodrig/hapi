<?php
declare(strict_types=1);

namespace test\labo86\hapi_core\local;

use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi_core\BuiltInServer;
use PHPUnit\Framework\TestCase;

class BuiltInServerTest extends TestCase
{

    public function assertJsonStringToArray(array $expected, string $actual, string $message = "") {
        $this->assertJson($actual);
        $this->assertEquals($expected, json_decode($actual, true), $message);
    }

    /**
     * @throws ExceptionWithData
     */
    public function testMakeRequest()
    {
        $server = new BuiltInServer(__DIR__ . '/resources/www');
        $server->run();

        $response = $server->makeRequest('get_method.php');

        $this->assertJsonStringToArray(['message' => 'request does not have parameter', 'data' => ['parameter_name' => 'method', 'available_parameter_list' => []]], $response);

        $this->assertStringContainsString('[200]: GET /get_method.php', $server->getStdErr());
    }

    /**
     * @throws ExceptionWithData
     */
    public function testServerError()
    {
        $server = new BuiltInServer(__DIR__ . '/resources/www');
        $server->run();



        $response = $server->makeRequest('syntax_error.php');

        $this->assertEquals("", $response);

        $this->assertStringContainsString('PHP Fatal error:  Uncaught Error: Call to a member function call() on null', $server->getStdErr());
    }

    /**
     * @throws ExceptionWithData
     */
    public function testSetEnvironment()
    {
        $server = new BuiltInServer(__DIR__ . '/resources/www');
        $server->setEnvironment([
            'VAR_1' => 'SOMETHING'
        ]);
        $server->run();


        $response = $server->makeRequest('get_env.php');

        $this->assertJson($response);
        $response = json_decode($response, true);
        $this->assertArrayHasKey("VAR_1", $response);
        $this->assertEquals('SOMETHING', $response['VAR_1']);

        $this->assertStringContainsString('[200]: GET /get_env.php', $server->getStdErr());


    }
}
