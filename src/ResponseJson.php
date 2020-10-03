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
        $this->mime_type = 'application/json;charset=utf-8';
    }

    public function send() {
        http_response_code($this->http_response_code);
        $json_response = json_encode($this->data, JSON_PRETTY_PRINT);
        header($this->getHeaderContentType());
        $this->sendHeaderList();
        echo $json_response;
    }

    public function getData() {
        return $this->data;
    }
}