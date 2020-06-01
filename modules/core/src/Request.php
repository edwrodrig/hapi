<?php
declare(strict_types=1);

namespace edwrodrig\hapi_core;

class Request
{
     protected array $params;

     public function __construct() {
         $request_method = $_SERVER['REQUEST_METHOD'];
         if ( $request_method == "GET" ) {
             $this->params = $_GET;
         } else if ( $request_method == "POST" ) {
             $this->params = $this->getPostParams();
         }

     }

    public function getMethod() : string {
        return $this->params['method'] ?? '';
    }

    public function getParams() : array {
         return $this->params;
    }

    protected function getContentType() : string {
        $content_type = $_SERVER["CONTENT_TYPE"];
        $elements = explode(";", $content_type, 2);
        return trim($elements[0]);
    }

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