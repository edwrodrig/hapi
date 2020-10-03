<?php
declare(strict_types=1);

namespace labo86\hapi;

abstract class Response
{
    protected string $mime_type;

    abstract public function send();

    public array $header_list = [];

    public int $http_response_code = 200;

    public function setMimeType($mime_type) {
        $this->mime_type = $mime_type;
    }

    public function setHttpResponseCode(int $code) {
        $this->http_response_code = $code;
    }

    public function getHeaderContentType() : string {
        return 'Content-Type: ' . $this->mime_type;
    }

    protected function sendHeaderList() {
        foreach ( $this->header_list as $header_line)
            header($header_line);
    }

    /**
     * Headers utiles:
     * <code>
     *'Expires: 0'
     *'Cache-Control: must-revalidate'
     *'Pragma: public'
     * </code>
     * <code>
     * 'Cache-Control:max-age=seconds
     * </code>
     * Ver headers de {@see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control cache control}
     * {@see https://sookocheff.com/post/api/effective-caching/}
     * {@see https://stackoverflow.com/questions/5321876/which-one-to-use-expire-header-last-modified-header-or-etags}
     * {@see https://blog.fortrabbit.com/mastering-http-caching}
     * @param string $header_line
     */
    public function addHeader(string $header_line) {
        $this->header_list[] = $header_line;
    }
}