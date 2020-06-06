<?php
declare(strict_types=1);

namespace test\labo86\hapi\local;


use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi_core\BuiltInServer;
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

    /**
     * Testeando examples/www2/index.php
     * @throws ExceptionWithData
     */
    public function testExampleWwwError()
    {

        $server = new BuiltInServer(__DIR__ . '/../../examples/www2');
        $server->setEnvironment([
            'ERROR_LOG_FILENAME' => $this->path . '/log'
        ]);
        $server->run();

        $response = $server->makeRequest('index.php');

        $this->assertJson($response);
        $response = json_decode($response, true);


        $this->assertFileExists($this->path . '/log');
        $log_content = file_get_contents($this->path . '/log');
        $this->assertJson($log_content);
        $log = json_decode($log_content, true);
        $this->assertEquals($response['i'], $log['i']);
    }

    /**
     * Testeando examples/www2/index.php
     * @throws ExceptionWithData
     */
    public function testExampleWwwOk()
    {

        $server = new BuiltInServer(__DIR__ . '/../../examples/www2');
        $server->setEnvironment([
            'ERROR_LOG_FILENAME' => $this->path . '/log'
        ]);
        $server->run();

        $response = $server->makeRequest('index.php?method=echo');
        $this->assertJson($response);
        $response = json_decode($response, true);
        $this->assertEquals([
            'message' => 'successful',
            'request_params' => [
                'method' => 'echo'
            ]
        ],  $response);

        $this->assertFileNotExists($this->path . '/log');

    }


}
