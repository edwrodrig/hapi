<?php
declare(strict_types=1);

namespace labo86\hapi_core;

abstract class Response
{
    abstract public function send();
}