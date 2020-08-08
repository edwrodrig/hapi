<?php
declare(strict_types=1);

namespace labo86\hapi_core;

use labo86\exception_with_data\ExceptionWithData;

class Request
{
     protected array $parameter_list;

    /**
     * @codeCoverageIgnore
     */
     public function __construct() {
         $request_method = $this->getServerVariable('REQUEST_METHOD');
         if ( $request_method == "GET" ) {
             $this->parameter_list = $_GET;
         } else if ( $request_method == "POST" ) {
             $this->parameter_list = $this->getPostParameterList();
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


    /**
     * Obtiene un parametros desde un nombre.
     * Este debee star en la variable GET o POST ya que busca en que que se encuentra es {@see $parameter_list} que es poblado en el constructor
     * @param string $parameter_name
     * @return string
     * @throws ExceptionWithData
     */
    public function getParameter(string $parameter_name) : string {
        if ( !isset($this->parameter_list[$parameter_name]) )
            throw new ExceptionWithData('request does not have parameter', [ 'parameter_name' => $parameter_name, 'available_parameters' => $this->parameter_list ]);
        else
            return $this->parameter_list[$parameter_name];
    }

    public function getMethod() : string {
        return $this->getParameterList()['method'] ?? '';
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function getParameterList() : array {
         return $this->parameter_list;
    }

    public function getContentType() : string {
        $content_type = $this->getServerVariable("CONTENT_TYPE");
        $elements = explode(";", $content_type, 2);
        return trim($elements[0]);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getPostParameterList() : array {
        $content_type = $this->getContentType();

        if ( $content_type == "application/json" ) {
            $contents = file_get_contents("php://input");
            return json_decode($contents, true);
        } else {
            return $_POST;
        }
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getFileParameterList() : array {
        return $_FILES;
    }

    /**
     * @param string $name
     * @return array|string[]
     * @throws ExceptionWithData
     */
    public function getFileParameter(string $name) : array {
        $files = $this->getFileParameterList();
        if ( !isset($files[$name]))
            throw new ExceptionWithData('file input not found in post params', [ 'name' => $name, 'files' => $files ]);

        $file_input = $files[$name];

        if ( !is_string($file_input['name']))
            throw new ExceptionWithData('file input is not a valid single file', [ 'data' => $file_input]);

        return [
            'name' => $file_input[$name]['name'],
            'type' => $file_input[$name]['type'],
            'tmp_name' => $file_input[$name]['tmp_name'],
            'size' => $file_input[$name]['size'],
        ];
    }

    /**
     * Obtener multiples archivos desde un input file pasado en la variable $_FILES
     * @param string $name
     * @return array
     * @throws ExceptionWithData
     */
    public function getFileListParameter(string $name) : array {
        $files = $this->getFileParameterList();
        if ( !isset($files[$name]))
            throw new ExceptionWithData('file input not found in post params', [ 'name' => $name, 'files' => $files ]);

        $file_list_input = $files[$name];

        if ( !is_array($file_list_input['name']))
            throw new ExceptionWithData('file input is not a valid multiple file', [ 'data' => $file_list_input]);

        $file_count = count($file_list_input);
        $file_list = [];
        for ( $i = 0 ; $i < $file_count ; $i++ ) {
            $file_list[] = [
                'name' => $file_list_input['name'][$i],
                'type' => $file_list_input['type'][$i],
                'tmp_name' => $file_list_input['tmp_name'][$i],
                'size' => $file_list_input['size'][$i],
            ];
        }
        return $file_list;
    }
}