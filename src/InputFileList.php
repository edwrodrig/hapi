<?php
declare(strict_types=1);

namespace labo86\hapi;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;

class InputFileList implements ArrayAccess, IteratorAggregate
{
    private array $data;

    private array $file_list;

    public function __construct(array $data) {
        $this->data = $data;
        $file_count = count($data['name']);
        $this->file_list = [];
        for ( $i = 0 ; $i < $file_count ; $i++ ) {
            $this->file_list[] = new InputFile([
                'name' => $data['name'][$i],
                'type' => $data['type'][$i],
                'tmp_name' => $data['tmp_name'][$i],
                'size' => $data['size'][$i],
                'error' => $data['error'][$i]
            ]);
        }
    }

    /**
     * @return array
     */
    public function getData() : array {
        return $this->data;
    }

    /**
     * @return ArrayIterator|InputFile[]
     */
    public function getIterator()
    {
        return new ArrayIterator($this->file_list);
    }

    public function offsetExists($offset)
    {
        return isset($this->file_list[$offset]);
    }

    /**
     * @param mixed $offset
     * @return InputFile
     */
    public function offsetGet($offset)
    {
        return $this->file_list[$offset];
    }

    public function offsetSet($offset, $value) {;}

    public function offsetUnset($offset) {;}
}