<?php
declare(strict_types=1);

namespace labo86\hapi;

use ArrayAccess;

class InputFile implements ArrayAccess
{
    private array $data;

    public function __construct($file_data) {
        $this->data = $file_data;
    }

    public function getName() : string {
        return $this->data['name'];
    }

    public function getTmpName() : string {
        return $this->data['tmp_name'];
    }

    public function getType() : string {
        return $this->data['type'];
    }

    public function getSize() {
        return $this->data['size'];
    }

    public function getData() : array {
        return $this->data;
    }

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset) {
        return $this->data[$offset];
    }
    public function offsetSet($offset, $value) {;}

    public function offsetUnset($offset) {;}

}