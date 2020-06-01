<?php
declare(strict_types=1);

namespace test\edwrodrig\hapi_core;

use edwrodrig\hapi_core\BuiltInServer;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    protected static BuiltInServer $server;

    public static function setUpBeforeClass(): void
    {
        self::$server = new BuiltInServer(__DIR__ . '/resources/www');
        self::$server->run();
    }

    public static function tearDownAfterClass(): void
    {
        self::$server->__destruct();
    }

    public function assertJsonStringToArray(array $expected, string $actual, string $message = "") {
        $this->assertJson($actual);
        $this->assertEquals($expected, json_decode($actual, true), $message);
    }

    public function getMethodProvider()
    {
        return [
            [[], "", ""],
            [["method" => "action"], "action", "?method=action"],
            [["param" => "action"], "", "?param=action"],
            [["method" => "action", "param" => "content"], "action", "?method=action&param=content"]
        ];
    }

    /**
     * @dataProvider getMethodProvider
     * @param array $expected_params
     * @param string $expected_method
     * @param string $query_params
     */
    public function testGetRequest(array $expected_params, string $expected_method, string $query_params)
    {
        $response = self::$server->makeRequest('get_method.php' . $query_params);
        $this->assertEquals($expected_method, $response);

        $response = self::$server->makeRequest('get_params.php' . $query_params);
        $this->assertJsonStringToArray($expected_params, $response);
    }

    /**
     * @dataProvider getMethodProvider
     */
    public function testJsonRequest()
    {
        $context  = stream_context_create([
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode(["method" => "action", "param" => "content"])
            ]
        ]);

        $response = self::$server->makeRequest('get_method.php', $context);
        $this->assertEquals("action", $response);

        $response = self::$server->makeRequest('get_params.php', $context);
        $this->assertJsonStringToArray(["method" => "action", "param" => "content"], $response);
    }

    /**
     * @dataProvider getMethodProvider
     */
    public function testJsonRequestCharset()
    {
        $context  = stream_context_create([
            'http' => [
                'header'  => "Content-type: application/json; charset=utf-8\r\n",
                'method'  => 'POST',
                'content' => json_encode(["method" => "action", "param" => "content"])
            ]
        ]);

        $response = self::$server->makeRequest('get_method.php', $context);
        $this->assertEquals("action", $response);

        $response = self::$server->makeRequest('get_params.php', $context);
        $this->assertJsonStringToArray(["method" => "action", "param" => "content"], $response);
    }

}
