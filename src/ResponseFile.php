<?php
declare(strict_types=1);

namespace labo86\hapi;

class ResponseFile extends Response
{
    protected string $filename;

    protected bool $is_attachment = true;

    /**
     * ResponseFile constructor.
     * Esta función representa un response de un archivo.
     * Ver {@see send()} para ver como manda los headers. Al final esto solo envía headers
     * @param string $filename
     */
    public function __construct(string $filename) {
        $this->filename = $filename;
        $this->mime_type = 'application/octet-stream';
    }

    public function setAsAttachment(bool $is_attachment) {
        $this->is_attachment = $is_attachment;
    }

    /**
     * Debería haber una cache policy para esto pero mejor a futuro
     * https://stackoverflow.com/questions/38180690/how-to-force-download-different-type-of-extension-file-php
     */
    public function send() {

        header($this->getHeaderContentType());
        if ( $this->is_attachment ) {
            header('Content-Disposition: attachment; filename="' . basename($this->filename) . '"');
            header('Content-Description: File Transfer');
        }
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