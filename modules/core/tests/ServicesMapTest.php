<?php
declare(strict_types=1);

namespace test\edwrodrig\hapi_core;

use edwrodrig\exception_with_data\ExceptionWithData;
use edwrodrig\hapi_core\ServicesMap;
use PHPUnit\Framework\TestCase;

class ServicesMapTest extends TestCase
{

    public function testGetServiceBasic()
    {
        try {

            $map = new ServicesMap();
            $map->getService("something");
            $this->fail("should throw");

        } catch (ExceptionWithData $exception) {
            $this->assertEquals("service not registered", $exception->getMessage());
            $this->assertEquals(["method_name" => "something"], $exception->getData());
        }



    }
}
