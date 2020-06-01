<?php
declare(strict_types=1);

namespace edwrodrig\hapi_core;

class ResponseFile extends Response
{
    public string $filename;

    /**
     * https://stackoverflow.com/questions/38180690/how-to-force-download-different-type-of-extension-file-php
     */
    public function send() {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($this->filename).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($this->filename));
        readfile($this->filename);
    }
}