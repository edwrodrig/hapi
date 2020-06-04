<?php
declare(strict_types=1);

namespace edwrodrig\hapi_core;

class Request
{
     protected array $params;

    /**
     * @codeCoverageIgnore
     */
     public function __construct() {
         $request_method = $this->getServerVariable('REQUEST_METHOD');
         if ( $request_method == "GET" ) {
             $this->params = $_GET;
         } else if ( $request_method == "POST" ) {
             $this->params = $this->getPostParams();
         }

     }

    /**
     * @codeCoverageIgnore
     * @param string $name
     * @return string
     */
     protected function getServerVariable(string $name) : string {
         return  $_SERVER[$name];
     }

    public function getMethod() : string {
        return $this->getParams()['method'] ?? '';
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function getParams() : array {
         return $this->params;
    }

    public function getContentType() : string {
        $content_type = $this->getServerVariable("CONTENT_TYPE");
        $elements = explode(";", $content_type, 2);
        return trim($elements[0]);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getPostParams() : array {
        $content_type = $this->getContentType();

        if ( $content_type == "application/json" ) {
            $contents = file_get_contents("php://input");
            return json_decode($contents, true);
        } else {
            return $_POST;
        }
    }
}