<?php
declare(strict_types=1);

namespace test\edwrodrig\hapi_core;

use edwrodrig\hapi_core\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    public function testGetMethodBasic()
    {

        $stub = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getParams'])
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->any())
            ->method('getParams')
            ->willReturn(['method' => 'something']);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals('something', $stub->getMethod());

    }

    public function testGetMethodNotMethod()
    {

        $stub = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getParams'])
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->any())
            ->method('getParams')
            ->willReturn([]);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals('', $stub->getMethod());

    }

    public function getContentTypeProvider()
    {
        return [
            ["application/json", "application/json"]
        ];
    }

    /**
     * @dataProvider getContentTypeProvider
     * @param $expected
     * @param $actual
     */

    public function testGetContentType(string $expected, string $actual) {
        $stub = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getServerVariable'])
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->any())
            ->method('getServerVariable')
            ->willReturn($actual);


        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($expected, $stub->getContentType());

    }
}
