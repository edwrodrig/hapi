<?php
declare(strict_types=1);

namespace test\edwrodrig\hapi;

use edwrodrig\hapi\Server;
use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{

    public function test_do_basic()
    {
        $action = new Server();
        $this->assertEquals("some return", $action->do());
    }


}
