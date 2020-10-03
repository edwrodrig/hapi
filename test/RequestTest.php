<?php
declare(strict_types=1);

namespace test\labo86\hapi;

use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi\InputFile;
use labo86\hapi\InputFileList;
use labo86\hapi\Request;
use PHPUnit\Framework\TestCase;
use stdClass;

class RequestTest extends TestCase
{

    public function testGetParameterBasic()
    {

        $request = new Request();
        $request->setParameterList(['method' => 'something']);

        /** @var Request $stub */
        $this->assertEquals('something', $request->getParameter('method'));

    }

    public function testGetParameterNotParameter()
    {
        try {
            $request = new Request();
            $request->setParameterList(['a' => '1']);


            /** @var Request $stub */
            $request->getParameter('method');


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
        $request = new Request();
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
        $request = new Request();
        $request->setParameterList(['method' => $actual]);

        /** @var Request $stub */
        $this->assertEquals($expected, $request->getStringParameter('method'));
    }

    public function getIntParameterProvider()
    {
        return [
            [2, "2"],
            [3, 3],
            [-23, -23],
            [-23, "-23"]
        ];
    }

    /**
     * @dataProvider getIntParameterProvider
     * @param $expected
     * @param $actual
     * @throws ExceptionWithData
     */
    public function testGetIntParameter(int $expected, $actual)
    {
        $request = new Request();
        $request->setParameterList(['method' => $actual]);

        /** @var Request $stub */
        $this->assertEquals($expected, $request->getIntParameter('method'));
    }

    public function getArrayParameterProvider()
    {
        return [
            [[], []],
            [[1,2,3], [1,2,3]],
            [['hola' => 'como'], ['hola' => 'como']]
        ];
    }

    /**
     * @dataProvider getArrayParameterProvider
     * @param $expected
     * @param $actual
     * @throws ExceptionWithData
     */
    public function testGetArrayParameter(array $expected, array $actual)
    {
        $request = new Request();
        $request->setParameterList(['method' => $actual]);

        /** @var Request $stub */
        $this->assertEquals($expected, $request->getArrayParameter('method'));
    }

    public function getFileParameterProvider() {
        return [
            [['name' => 'hola.txt', 'type' => 'text/plain', 'size' => 10, 'tmp_name' => '/tmp/adfa', 'error' => 0]]
        ];
    }

    /**
     * @dataProvider getFileParameterProvider
     * @param $expected
     * @param $actual
     * @throws ExceptionWithData
     */
    public function testGetFileParameter(array $actual)
    {
        $request = new Request();
        $request->setFileParameterList(['method' => $actual]);

        /** @var Request $stub */
        $input_file = $request->getFileParameter('method');
        $this->assertInstanceOf(InputFile::class, $input_file);
        $this->assertEquals($actual, $input_file->getData());
    }

    public function getFileListParameterProvider() {
        return [
            [[
                'name' => ['hola.txt', 'image.jpg'],
                'type' => ['text/plain', 'image/jpeg'],
                'size' => [10, 13],
                'tmp_name' => ['/tmp/adfa', '/tmp/adfb'],
                'error' => [0, 0]
            ]]
        ];
    }

    /**
     * @dataProvider getFileListParameterProvider
     * @param $expected
     * @param $actual
     * @throws ExceptionWithData
     */
    public function testGetFileListParameter(array $actual)
    {
        $request = new Request();
        $request->setFileParameterList(['method' => $actual]);

        /** @var Request $stub */
        $input_file = $request->getFileListParameter('method');
        $this->assertInstanceOf(InputFileList::class, $input_file);
        $this->assertEquals($actual, $input_file->getData());
        $this->assertEquals($actual['name'][0],$input_file[0]->getData()['name']);
        $this->assertEquals($actual['name'][1],$input_file[1]->getData()['name']);
    }

    public function getStringParameterErrorProvider()
    {
        return [
            [["type" => "array", "value" => []], []],
            [["type" => "boolean", "value" => true ], true],
            [["type" => "boolean", "value" => false ], false],
            [["type" => "object", "value" => new stdClass()], new stdClass()],
        ];
    }

    /**
     * @dataProvider getStringParameterErrorProvider
     * @param $expected
     * @param $actual
     */
    public function testGetStringParameterError($expected, $actual)
    {
        $request = new Request();
        $request->setParameterList(['method' => $actual]);

        try {
            $request->getStringParameter('method');
            $this->fail("should throw");

        } catch (ExceptionWithData $exception) {
            $expected['name'] = 'method';
            $this->assertEquals("parameter is not a string", $exception->getMessage());
            $this->assertEquals($expected, $exception->getData());
        }

    }

    public function getIntParameterErrorProvider()
    {
        return [
            [["type" => "array", "value" => []], []],
//            [["type" => "boolean", "value" => true ], true],
            [["type" => "boolean", "value" => false ], false],
            [["type" => "object", "value" => new stdClass()], new stdClass()],
            [["type" => "string", "value" => "hello_world"], "hello_world"],
            [["type" => "string", "value" => "0.12"], "0.12"]
        ];
    }

    /**
     * @dataProvider getIntParameterErrorProvider
     * @param $expected
     * @param $actual
     */
    public function testGetIntParameterError($expected, $actual)
    {
        $request = new Request();
        $request->setParameterList(['method' => $actual]);

        try {
            $request->getIntParameter('method');
            $this->fail("should throw");

        } catch (ExceptionWithData $exception) {
            $expected['name'] = 'method';
            $this->assertEquals("parameter is not an integer", $exception->getMessage());
            $this->assertEquals($expected, $exception->getData());
        }
    }

    public function getArrayParameterErrorProvider()
    {
        return [
            [["type" => "boolean", "value" => true ], true],
            [["type" => "boolean", "value" => false ], false],
            [["type" => "object", "value" => new stdClass()], new stdClass()],
            [["type" => "string", "value" => "hello_world"], "hello_world"],
            [["type" => "string", "value" => "0.12"], "0.12"]
        ];
    }

    /**
     * @dataProvider getArrayParameterErrorProvider
     * @param $expected
     * @param $actual
     */
    public function testGetArrayParameterError($expected, $actual)
    {
        $request = new Request();
        $request->setParameterList(['method' => $actual]);

        try {
            $request->getArrayParameter('method');
            $this->fail("should throw");

        } catch (ExceptionWithData $exception) {
            $expected['name'] = 'method';
            $this->assertEquals("parameter is not an array", $exception->getMessage());
            $this->assertEquals($expected, $exception->getData());
        }
    }

    public function getFileParameterErrorProvider()
    {
        return [
            [["type" => "array", "value" => ['name' => []] ], ['name' => []]]
        ];
    }

    /**
     * @dataProvider getFileParameterErrorProvider
     * @param $expected
     * @param $actual
     */
    public function testGetFileParameterError($expected, $actual)
    {
        $request = new Request();
        $request->setFileParameterList(['method' => $actual]);


        try {
            $request->getFileParameter('method');
            $this->fail("should throw");

        } catch (ExceptionWithData $exception) {
            $expected['name'] = 'method';
            $this->assertEquals("parameter is not a single file input", $exception->getMessage());
            $this->assertEquals($expected, $exception->getData());
        }
    }

    public function getFileListParameterErrorProvider()
    {
        return [
            [["type" => "array", "value" => ['name' => "some_name"] ], ['name' => "some_name"]]
        ];
    }

    /**
     * @dataProvider getFileListParameterErrorProvider
     * @param $expected
     * @param $actual
     */
    public function testGetFileListParameterError($expected, $actual)
    {
        $request = new Request();
        $request->setFileParameterList(['method' => $actual]);

        try {
            $request->getFileListParameter('method');
            $this->fail("should throw");

        } catch (ExceptionWithData $exception) {
            $expected['name'] = 'method';
            $this->assertEquals("parameter is not a multiple file input", $exception->getMessage());
            $this->assertEquals($expected, $exception->getData());
        }
    }
}
