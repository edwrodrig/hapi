<?php
declare(strict_types=1);

namespace labo86\hapi;


class ResponseJson extends Response
{
    /**
     * @var mixed
     */
    protected $data;

    public function __construct($data) {
        $this->data = $data;
        $this->mime_type = 'Content-Type: application/json;charset=utf-8';
    }

    public function send() {
        $json_response = json_encode($this->data, JSON_PRETTY_PRINT);
        header($this->getHeaderContentType());
        echo $json_response;
    }

    public function getData() {
        return $this->data;
    }
}