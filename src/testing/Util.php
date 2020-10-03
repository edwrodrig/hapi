<?php
declare(strict_types=1);

namespace labo86\hapi\testing;

use labo86\hapi\InputFile;
use labo86\hapi\InputFileList;

class Util
{
    public static function createInputFileListFromFiles(array $file_path_list) : InputFileList {
        $result = [
            'name' => [],
            'type' => [],
            'tmp_name' => [],
            'size' => []
        ];

        foreach ( $file_path_list as $file_path ) {
            $file_info = new \SplFileInfo($file_path);
            $result['name'][] = $file_info->getBasename();
            $result['tmp_name'][] = $file_info->getRealPath();
            $result['size'][] = $file_info->getSize();
            $result['type'][] = mime_content_type($file_info->getRealPath());
            $result['error'][] = 0;
        }

        return new InputFileList($result);
    }

    public static function createInputFileFromFile(string $file_path) : InputFile {
        $result = [];

        $file_info = new \SplFileInfo($file_path);
        $result['name'] = $file_info->getBasename();
        $result['tmp_name'] = $file_info->getRealPath();
        $result['size'] = $file_info->getSize();
        $result['type'] = mime_content_type($file_info->getRealPath());
        $result['error'] = 0;

        return new InputFile($result);
    }

}