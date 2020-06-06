<?php
declare(strict_types=1);

namespace test\labo86\hapi_core;

use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi_core\ServiceMap;
use PHPUnit\Framework\TestCase;

class ServiceMapTest extends TestCase
{

    public function testGetServiceBasic()
    {
        try {

            $map = new ServiceMap();
            $map->getService("something");
            $this->fail("should throw");

        } catch (ExceptionWithData $exception) {
            $this->assertEquals("service not registered", $exception->getMessage());
            $this->assertEquals(["method_name" => "something"], $exception->getData());
        }



    }
}
