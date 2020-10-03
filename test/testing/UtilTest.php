<?php

namespace test\labo86\hapi\testing;

use labo86\hapi\testing\Util;
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{

    public function testCreateInputFileListFromFiles()
    {
        $file_name = __DIR__ . '/../local/resources/data.txt';
        $files = Util::createInputFileListFromFiles([$file_name]);
        $expected = [
            'name' => ['data.txt'],
            'tmp_name' => [realpath($file_name)],
            'size' => [9],
            'type' => ['text/plain'],
            'error' => [0]
        ];
        $this->assertEquals($expected, $files->getData());
    }

    public function testCreateInputFileFromFile()
    {
        $file_name = __DIR__ . '/../local/resources/data.txt';
        $file = Util::createInputFileFromFile($file_name);
        $expected = [
            'name' => 'data.txt',
            'tmp_name' => realpath($file_name),
            'size' => 9,
            'type' => 'text/plain',
            'error' => 0
        ];
        $this->assertEquals($expected, $file->getData());
    }
}
