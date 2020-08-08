<?php
declare(strict_types=1);

namespace test\labo86\hapi_core;

use labo86\hapi_core\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    public function testGetMethodBasic()
    {

        $stub = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getParameterList'])
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->any())
            ->method('getParameterList')
            ->willReturn(['method' => 'something']);

        /** @var Request $stub */
        $this->assertEquals('something', $stub->getMethod());

    }

    public function testGetMethodNotMethod()
    {

        $stub = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getParameterList'])
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->any())
            ->method('getParameterList')
            ->willReturn([]);

        /** @var Request $stub */
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


        /** @var Request $stub */
        $this->assertEquals($expected, $stub->getContentType());

    }
}
