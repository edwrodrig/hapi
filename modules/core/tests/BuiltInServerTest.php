<?php
declare(strict_types=1);

namespace test\edwrodrig\hapi_core;

use edwrodrig\exception_with_data\ExceptionWithData;
use edwrodrig\hapi_core\BuiltInServer;
use PHPUnit\Framework\TestCase;

class BuiltInServerTest extends TestCase
{
    public function testGetCommand()
    {
        $document_root = __DIR__ . '/local/resources/www';
        $server = new BuiltInServer($document_root);
        $this->assertStringContainsString($document_root, $server->getCommand());
    }

    public function testGetBaseUrl() {
        $document_root = __DIR__ . '/local/resources/www';
        $server = new BuiltInServer($document_root);
        $this->assertStringContainsString("http://localhost", $server->getBaseUrl());
    }

    public function testRun()
    {
        $stub = $this->getMockBuilder(BuiltInServer::class)
            ->onlyMethods(['getCommand'])
            ->setConstructorArgs([__DIR__ . '/local/resources/www'])
            ->getMock();

        $stub->expects($this->any())
            ->method('getCommand')
            ->willReturn('>&2 echo "Start"; while sleep 1; do (>&2 echo "Hi" ); done');

        /** @var BuiltInServer $stub */
        $this->assertTrue($stub->run());

        $output = $stub->getStdErr();
        $this->assertStringStartsWith("Start", $output);
    }


    public function testGetStdErrNotStarted()
    {
        $document_root = __DIR__ . '/local/resources/www';
        $server = new BuiltInServer($document_root);
        $this->assertEquals("", $server->getStdErr());
    }

    public function testSetEnvironment()
    {
        $stub = $this->getMockBuilder(BuiltInServer::class)
            ->onlyMethods(['getCommand'])
            ->setConstructorArgs([__DIR__ . '/local/resources/www'])
            ->getMock();

        $stub->expects($this->any())
            ->method('getCommand')
            ->willReturn('>&2 echo $VAR_1');

        /** @var BuiltInServer $stub */
        $stub->setEnvironment([
            'VAR_1' => 'SOMETHING'
        ]);

        $stub->run();


        $output = $stub->getStdErr();
        $this->assertStringStartsWith("SOMETHING", $output);

    }

    /**
     * @throws ExceptionWithData
     */
    public function testMakeRequest()
    {
        $stub = $this->getMockBuilder(BuiltInServer::class)
            ->onlyMethods(['getCommand', 'getBaseUrl'])
            ->getMock();

        $stub->expects($this->any())
            ->method('getCommand')
            ->willReturn('while sleep 1; do echo "Hi"; done');

        $stub->expects($this->any())
            ->method('getBaseUrl')
            ->willReturn('https://github.com');

        /** @var BuiltInServer $stub */
        $stub->run();

        $content = $stub->makeRequest('edwrodrig/hapi/blob/master/README.md');
        $this->assertNotEmpty($content);
    }

    /**
     * @throws ExceptionWithData
     */
    public function testMakeRequestFail()
    {
        $stub = $this->getMockBuilder(BuiltInServer::class)
            ->onlyMethods(['getCommand', 'getBaseUrl'])
            ->getMock();

        $stub->expects($this->any())
            ->method('getCommand')
            ->willReturn('while sleep 1; do echo "Hi"; done');

        $stub->expects($this->any())
            ->method('getBaseUrl')
            ->willReturn('https://github.com');

        /** @var BuiltInServer $stub */
        $stub->run();

        $content = $stub->makeRequest('edwrodrig/hapi/blob/master/not_existent.md');
        $this->assertNotEmpty($content);
    }

    public function testMakeRequestNotRunning()
    {
        $stub = $this->getMockBuilder(BuiltInServer::class)
            ->onlyMethods(['getBaseUrl'])
            ->getMock();

        $stub->expects($this->any())
            ->method('getBaseUrl')
            ->willReturn('https://github.com');


        try {
            /** @var BuiltInServer $stub */
            $stub->makeRequest('edwrodrig/hapi/blob/master/not_existent.md');
            $this->fail("should throw");

        } catch (ExceptionWithData $exception) {
            $this->assertEquals("server is not running", $exception->getMessage());
            $this->assertEquals(['document_root' => '.'], $exception->getData());
        }
    }

}
