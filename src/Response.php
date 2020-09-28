<?php
declare(strict_types=1);

namespace labo86\hapi;

abstract class Response
{
    protected string $mime_type;

    abstract public function send();

    public function setMimeType($mime_type) {
        $this->mime_type = $mime_type;
    }

    public function getHeaderContentType() : string {
        return 'Content-Type: ' . $this->mime_type;
    }
}