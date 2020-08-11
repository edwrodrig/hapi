<?php
declare(strict_types=1);

namespace labo86\hapi_core;


class ResponseJson extends Response
{
    /**
     * @var mixed
     */
    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function send() {
        $json_response = json_encode($this->data, JSON_PRETTY_PRINT);
        header('Content-Type: application/json;charset=utf-8');
        echo $json_response;
    }

    public function getData() {
        return $data;
    }
}