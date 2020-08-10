<?php
declare(strict_types=1);

namespace labo86\hapi_core;

use labo86\exception_with_data\ExceptionWithData;

/**
 * Class Request.
 * Esta clase representa un request recibido.
 * Está hecha para facilitar la detección de un parametro con nombre method que puede venir como POST o GET.
 * Para obtener parametros use el metodo {@see getParameter()} que obtendra los apraemtros que vienen por GET o por POST según sea el caso.
 * Los parametros que vienen por $FILE se recuperan con los métodos {@see getFileParameter()} si es un file input que selecciona un archivo
 * o {@see getFileListParameter()}  si el file input soporta multiples archivos.
 *
 * @package labo86\hapi_core
 */
class Request
{
     protected array $parameter_list;

    /**
     * @codeCoverageIgnore
     * @param string $name
     * @return string
     */
    protected function getServerVariable(string $name) : string {
         return  $_SERVER[$name];
    }

    public function hasParameter(string $parameter_name) : bool {
        $parameter_list = $this->getParameterList();
        return isset($parameter_list[$parameter_name]);
    }

    public function hasFileParameter(string $parameter_name) : bool {
        $parameter_list = $this->getFileParameterList();
        return isset($parameter_list[$parameter_name]);
    }

    /**
     * Obtiene un parámetros desde un nombre.
     * Este debe estar en la variable GET o POST ya que busca en en el resultado de {@see $parameter_list} que es poblado en el constructor
     * @param string $parameter_name
     * @return mixed
     * @throws ExceptionWithData
     */
    public function getParameter(string $parameter_name) {
        $parameter_list = $this->getParameterList();
        if ( !$this->hasParameter($parameter_name) )
            throw new ExceptionWithData('request does not have parameter', [ 'parameter_name' => $parameter_name, 'available_parameter_list' => $parameter_list ]);
        else
            return $parameter_list[$parameter_name];
    }

    /**
     * Obtiene los parametros GET y POST según sea la naturaleza del request.
     * En el caso de obtener las variables POST usa el metodo {@see getPostParameterList()}.
     * El request se obtiene de la variable de servidor {@see https://www.php.net/manual/es/reserved.variables.server.php REQUEST_METHOD}.
     *  Este metodo es lazy. Solo necesita cargarse una vez.
     * @codeCoverageIgnore
     * @return array
     * @throws ExceptionWithData
     */
    public function getParameterList() : array {
        if ( !isset($this->parameter_list) ) {
            $request_method = $this->getServerVariable('REQUEST_METHOD');
            if ( $request_method == "GET" )
                $this->parameter_list = $_GET;
            else if ( $request_method == "POST" )
               $this->parameter_list = $this->getPostParameterList();
        }
        return $this->parameter_list;
    }

    public function getContentType() : string {
        $content_type = $this->getServerVariable("CONTENT_TYPE");
        $elements = explode(";", $content_type, 2);
        return trim($elements[0]);
    }

    /**
     * Obtienen los parámetros pasados por post. Si el contenido del request es un objeto json entonces usas sus valores como  los parametros de POST,
     * en caso contrario usa directamente la variable POST. A[un no se valida que el json sea un objeto.
     * Esta función es usada por {@see getParameterList()}
     *
     * @codeCoverageIgnore
     * @throws ExceptionWithData
     */
    protected function getPostParameterList() : array {
        $content_type = $this->getContentType();

        if ( $content_type == "application/json" ) {
            $contents = file_get_contents("php://input");
            $decoded_json = json_decode($contents, true);

            if ( $decoded_json === null )
                throw new ExceptionWithData('post content is not a valid json', ['contents' => $contents]);

            if ( !is_array($decoded_json) )
                throw new ExceptionWithData('json in post is not an array', ['contents' => $contents]);

            return $decoded_json;

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
/*
    public function getBoolParameter(string $name) : bool {
        $value = $this->getParameter($name);

    }

    public function getIntParameter(string $name) : int {
        $value = $this->getParameter($name);
        $int_value = intval($value);
        if ( $int_value === )
            throw new ExceptionWithData('parameter is not a string', [
                'name' => $name,
                'type' => gettype($value),
                'value' => $value
            ]);
        } else if ( is_numeric())
        } else {
            return strval($value);
        }
    }
    */

    public function getStringParameter(string $name) : string {
        $value = $this->getParameter($name);
        if ( is_array($value) ) {
            throw new ExceptionWithData('parameter is not a string', [
                'name' => $name,
                'type' => gettype($value),
                'value' => $value
            ]);
        } else if ( is_object($value) ) {
            throw new ExceptionWithData('parameter is not a string', [
                'name' => $name,
                'type' => gettype($value),
                'value' => print_r($value, true)
            ]);
        } else if ( is_bool($value)) {
            throw new ExceptionWithData('parameter is not a string', [
                'name' => $name,
                'type' => gettype($value),
                'value' => $value
            ]);
        } else {
            return strval($value);
        }
    }
/*
    public function getArrayParameter(string $name) : array {
        if ( $this->hasParameter($name) ) {
            $value = $this->getParameter($name);

        } else if ( $this->hasFileParameter($name))
            $this->getFil
        }
    }
*/
    /**
     * Obtiene un parametro que viene de un input file. El input file tiene que ser un archivo solo.
     * Este archivo devuelve un arreglo con los datos que vienen de $FILE que.
     * El array tiene las siguientes llaves:
     *  - name
     *  - type
     *  - tmp_name
     *  - size
     * Para mes información ver {@see https://www.php.net/manual/es/features.file-upload.post-method.php}
     * @param string $name
     * @return array|string[]
     * @throws ExceptionWithData
     */
    public function getFileParameter(string $name) : array {
        $files = $this->getFileParameterList();
        if ( !$this->hasFileParameter($name) )
            throw new ExceptionWithData('file input not found in post params', [ 'name' => $name, 'files' => $files ]);

        $file_input = $files[$name];

        if ( !is_string($file_input['name']))
            throw new ExceptionWithData('file input is not a valid single file', [ 'data' => $file_input]);

        return [
            'name' => $file_input['name'],
            'type' => $file_input['type'],
            'tmp_name' => $file_input['tmp_name'],
            'size' => $file_input['size'],
        ];
    }

    /**
     * Obtener multiples archivos desde un input file pasado en la variable $_FILES
     * Muy similar a {@see getFileParameter()} pero que funciona para file input multiples.
     * Entrega una array con array de ficheros con las llaves:
     *  - name
     *  - type
     *  - tmp_name
     *  - size
     * Lanza excepciones si existe un error.
     * @param string $name
     * @return array
     * @throws ExceptionWithData
     */
    public function getFileListParameter(string $name) : array {
        $files = $this->getFileParameterList();
        if ( !$this->hasFileParameter($name) )
            throw new ExceptionWithData('file input not found in post params', [ 'name' => $name, 'files' => $files ]);

        $file_list_input = $files[$name];

        if ( !is_array($file_list_input['name']))
            throw new ExceptionWithData('file input is not a valid multiple file', [ 'data' => $file_list_input]);

        $file_count = count($file_list_input['name']);
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