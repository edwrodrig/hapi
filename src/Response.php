<?php
declare(strict_types=1);

namespace labo86\hapi;

/**
 * Clase base de Respueta que tambien sirve para enviar respuestas sin cuerpo
 * Class Response
 * @package labo86\hapi
 */
class Response
{
    protected string $mime_type;

    public array $header_list = [];

    public int $http_response_code = 200;

    protected array $cookie_map = [];

    public function setMimeType($mime_type) {
        $this->mime_type = $mime_type;
    }

    /**
     * Setea las cookies de la misma forma que se hace con la funcion {@see setcookie()}
     * @param string $name
     * @param string $value
     * @param array $options
     */
    public function setCookie(string $name, string $value, array $options = []) {
        $this->cookie_map[$name] = [
            'value' => $value,
            'options' => $options
        ];
    }

    /**
     * Codigos
     * 200 OK
     * 400 Not found
     * 500 Internal Error
     * @param int $code
     */
    public function setHttpResponseCode(int $code) {
        $this->http_response_code = $code;
    }

    public function getHeaderContentType() : string {
        return 'Content-Type: ' . $this->mime_type;
    }

    /**
     * La llave es el nombre de la cookie,
     * el valor es un arreglo con value y options de acuerdo a la funcion {@see setcookie()}
     * @return array
     */
    public function getCookieMap() : array {
        return $this->cookie_map;
    }

    protected function sendCookies() {
        foreach ( $this->cookie_map as $name => $contents ) {
            setcookie($name, $contents['value'], $contents['options']);
        }
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

    public function send() {
        http_response_code($this->http_response_code);
        $this->sendCookies();
        $this->sendHeaderList();
    }
}