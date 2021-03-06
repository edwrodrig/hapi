<?php
declare(strict_types=1);

namespace test\labo86\hapi;

use labo86\exception_with_data\MessageMapperArray;
use labo86\hapi\Controller;
use labo86\hapi\Request;
use labo86\hapi\Response;
use labo86\hapi\ResponseJson;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{

    /**
     * @var false|string
     */
    private $path;

    public function setUp() : void {
        $this->path =  tempnam(__DIR__, 'demo_phar');

        unlink($this->path);
        mkdir($this->path, 0777);
    }

    public function tearDown() : void {
        exec('rm -rf ' . $this->path);
    }

    public function getControllerForTest(array $params, string $method = 'POST') : Controller
    {
        $request = new Request();
        $request->setHttpMethod($method);
        $request->setParameterList($params);


        $controller = $this->getMockBuilder(Controller::class)
            ->onlyMethods(['getRequest'])
            ->getMock();

        $controller->expects($this->any())
            ->method('getRequest')
            ->willReturn($request);

        $controller->setErrorLogFilename($this->path . '/log');
        $controller->setMessageMapper(new MessageMapperArray([]));
        /** @var Controller $controller */
        return $controller;
    }

    public function assertResponse(Controller $controller) : string {
        ob_start();
        $controller->run();
        return ob_get_clean();
    }

    public function assertResponseJson(Controller $controller) : array {
        $response = $this->assertResponse($controller);
        $this->assertJson($response);
        return json_decode($response, true);
    }

    public function assertLog() : array {
        $this->assertFileExists($this->path . '/log');
        $log_content = file_get_contents($this->path . '/log');
        $this->assertJson($log_content);
        return json_decode($log_content, true);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRun()
    {
        $controller = $this->getControllerForTest(['method' => 'echo']);

        $controller->getServiceMap()->registerService('echo', function(Request $request) : Response {
             return new ResponseJson(['method' => $request->getParameter('method'), 'return' => 'something']);
        });

        $response = $this->assertResponseJson($controller);
        $this->assertEquals(['method' => 'echo' , 'return' => 'something'], $response);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRunException()
    {
        $controller = $this->getControllerForTest(['method' => 'echo']);


        $response = $this->assertResponseJson($controller);

        $log = $this->assertLog();

        // obtienen el http response code
        $error_code = http_response_code();

        $this->assertEquals(400, $error_code);

        $this->assertEquals($response['i'], $log['i']);
        $this->assertArrayHasKey('t', $log);
    }

    /**
     * @runInSeparateProcess
     */
    public function testOptions()
    {
        $controller = $this->getControllerForTest([], 'OPTIONS');


        $response = $this->assertResponse($controller);

        $this->assertEquals("",$response);

        // obtienen el http response code
        $error_code = http_response_code();

        $this->assertEquals(204, $error_code);
    }


}
