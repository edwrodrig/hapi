<?php
declare(strict_types=1);

namespace test\labo86\hapi;

use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi\ServiceFunctionReflector;
use labo86\hapi_core\Request;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;

class ServiceFunctionReflectorTest extends TestCase
{

    public function testGetParameterTypeBasic()
    {
        $callback = function(string $param_0, float $param_1, Request $param_2, array $param_3, bool $param_4, int $param_5) {
            return 1;
        };

        $reflection_function = new ReflectionFunction($callback);
        $parameters = $reflection_function->getParameters();
        $this->assertEquals('string', ServiceFunctionReflector::getParameterType($parameters[0]->getType()));
        $this->assertEquals(Request::class, ServiceFunctionReflector::getParameterType($parameters[2]->getType()));
        $this->assertEquals('array', ServiceFunctionReflector::getParameterType($parameters[3]->getType()));
        $this->assertEquals('bool', ServiceFunctionReflector::getParameterType($parameters[4]->getType()));
        $this->assertEquals('int', ServiceFunctionReflector::getParameterType($parameters[5]->getType()));

        try {
            ServiceFunctionReflector::getParameterType($parameters[1]->getType());
            $this->fail("should throw");

        } catch (ExceptionWithData $exception) {
            $this->assertEquals("service parameter type is unsupported", $exception->getMessage());
            $this->assertEquals([ 'type' => 'float'], $exception->getData());
        }
    }

    public function testGetParameterInfo() {
        $callback = function(string $param_0, $param_1, Request $param_2, array $param_3, bool $param_4, int $param_5) {
            return 1;
        };

        $reflection_function = new ReflectionFunction($callback);
        $parameters = $reflection_function->getParameters();
        $this->assertEquals(['name' => 'param_0', 'type' => 'string'], ServiceFunctionReflector::getParameterInfo($parameters[0]));
        $this->assertEquals(['name' => 'param_1', 'type' => 'string'], ServiceFunctionReflector::getParameterInfo($parameters[1]));
        $this->assertEquals(['name' => 'param_2', 'type' => Request::class], ServiceFunctionReflector::getParameterInfo($parameters[2]));
        $this->assertEquals(['name' => 'param_3', 'type' => 'array'], ServiceFunctionReflector::getParameterInfo($parameters[3]));
        $this->assertEquals(['name' => 'param_4', 'type' => 'bool'], ServiceFunctionReflector::getParameterInfo($parameters[4]));
        $this->assertEquals(['name' => 'param_5', 'type' => 'int'], ServiceFunctionReflector::getParameterInfo($parameters[5]));

    }
}
