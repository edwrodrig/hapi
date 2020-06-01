<?php
declare(strict_types=1);

namespace edwrodrig\hapi_core;

abstract class Response
{
    abstract public function send();
}