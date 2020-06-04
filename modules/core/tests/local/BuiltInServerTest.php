<?php
declare(strict_types=1);

namespace test\edwrodrig\hapi_core\local;

use edwrodrig\hapi_core\BuiltInServer;
use PHPUnit\Framework\TestCase;

class BuiltInServerTest extends TestCase
{
    public function testMakeRequest()
    {
        $server = new BuiltInServer(__DIR__ . '/resources/www');
        $server->run();

        $response = $server->makeRequest('get_method.php');

        $this->assertEquals("", $response);

        $this->assertStringContainsString('[200]: GET /get_method.php', $server->getStdErr());
    }

    public function testServerError()
    {
        $server = new BuiltInServer(__DIR__ . '/resources/www');
        $server->run();



        $response = $server->makeRequest('syntax_error.php');

        $this->assertEquals("", $response);

        $this->assertStringContainsString('PHP Fatal error:  Uncaught Error: Call to a member function call() on null', $server->getStdErr());
    }

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
