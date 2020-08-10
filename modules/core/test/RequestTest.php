<?php
declare(strict_types=1);

namespace test\labo86\hapi_core;

use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi_core\Request;
use PHPUnit\Framework\TestCase;
use stdClass;

class RequestTest extends TestCase
{

    public function testGetParameterBasic()
    {

        $stub = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getParameterList'])
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->any())
            ->method('getParameterList')
            ->willReturn(['method' => 'something']);

        /** @var Request $stub */
        $this->assertEquals('something', $stub->getParameter('method'));

    }

    public function testGetParameterNotParameter()
    {
        try {
            $stub = $this->getMockBuilder(Request::class)
                ->onlyMethods(['getParameterList'])
                ->disableOriginalConstructor()
                ->getMock();

            $stub->expects($this->any())
                ->method('getParameterList')
                ->willReturn(['a' => '1']);

            /** @var Request $stub */
            $stub->getParameter('method');


                $this->fail("should throw");
        } catch (ExceptionWithData $exception) {
            $this->assertEquals("request does not have parameter", $exception->getMessage());
            $this->assertEquals(['parameter_name' => 'method',
                                'available_parameter_list' => ['a' => '1']], $exception->getData());
        }

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

    public function getStringParameterProvider()
    {
        return [
            ["something", "something"],
            ["2", "2"],
            ["2", 2]
        ];
    }

    /**
     * @dataProvider getStringParameterProvider
     * @param $expected
     * @param $actual
     * @throws ExceptionWithData
     */
    public function testGetStringParameter(string $expected, $actual)
    {
        $stub = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getParameterList'])
            ->getMock();

        $stub->expects($this->any())
            ->method('getParameterList')
            ->willReturn(['method' => $actual]);

        /** @var Request $stub */
        $this->assertEquals($expected, $stub->getStringParameter('method'));
    }

    public function getStringParameterErrorProvider()
    {
        return [
            [["type" => "array", "value" => []], []],
            [["type" => "boolean", "value" => true ], true],
            [["type" => "boolean", "value" => false ], false],
            [["type" => "object", "value" => "stdClass Object\n(\n)\n"], new stdClass()],
        ];
    }

    /**
     * @dataProvider getStringParameterErrorProvider
     * @param $expected
     * @param $actual
     * @throws ExceptionWithData
     */
    public function testGetStringParameterError($expected, $actual)
    {
        $stub = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getParameterList'])
            ->getMock();

        $stub->expects($this->any())
            ->method('getParameterList')
            ->willReturn(['method' => $actual]);

        try {
            $stub->getStringParameter('method');
            $this->fail("should throw");

        } catch (ExceptionWithData $exception) {
            $expected['name'] = 'method';
            $this->assertEquals("parameter is not a string", $exception->getMessage());
            $this->assertEquals($expected, $exception->getData());
        }

    }
}
