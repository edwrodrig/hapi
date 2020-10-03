<?php
declare(strict_types=1);

namespace labo86\hapi;

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
        $this->mime_type = 'application/octet-stream';
    }

    public function setAsAttachment() {
        $this->setHeaderContentAttachment();
    }

    public function setHeaderContentAttachment() {
        $this->addHeader('Content-Disposition: attachment; filename="' . basename($this->filename) . '"');
    }

    public function setHeaderContentLength() {
        $this->addHeader('Content-Length: ' . filesize($this->filename));
    }

    /**
     * Debería haber una cache policy para esto pero mejor a futuro
     * https://stackoverflow.com/questions/38180690/how-to-force-download-different-type-of-extension-file-php
     */
    public function send() {
        $this->http_response_code($this->http_response_code);
        header($this->getHeaderContentType());
        $this->sendHeaderList();
        readfile($this->filename);
    }

    public function getFilename() : string {
        return $this->filename;
    }
}