<?php
declare(strict_types=1);

namespace labo86\hapi_core;

class ResponseFile extends Response
{
    protected string $filename;

    /**
     * ResponseFile constructor.
     * Esta función representa un response de un archivo.
     * Ver {@see send()} para ver como manda los headers. Al final esto solo envía headers
     * @param string $filename
     */
    public function __construct(string $filename) {
        $this->filename = $filename;
    }

    /**
     * Debería haber una cache policy para esto pero mejor a futuro
     * https://stackoverflow.com/questions/38180690/how-to-force-download-different-type-of-extension-file-php
     */
    public function send() {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($this->filename).'"');
        //creo que estos headers de cache no son los mejores. Hay que investigar
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($this->filename));
        readfile($this->filename);
    }

    public function getFilename() : string {
        return $this->filename;
    }
}