<?php
declare(strict_types=1);

namespace labo86\hapi;


use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi_core\Request;
use ReflectionFunctionAbstract;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use Throwable;

class ServiceFunctionReflector
{

    /**
     * @param ReflectionFunctionAbstract $reflection_function
     * @return array
     * @throws ExceptionWithData
     */
    public static function getParameterInfoList(ReflectionFunctionAbstract $reflection_function) : array {
        $reflection_parameter_list = $reflection_function->getParameters();

        $parameter_info_list = [];
        $exception_list = [];
        foreach ( $reflection_parameter_list as $reflection_parameter ) {
            try {
                $parameter_info_list[] = self::getParameterInfo($reflection_parameter);
            } catch ( Throwable $exception ) {
                $exception_list[] = $exception;
            }
        }

        if ( !empty($exception_list) ) {
            throw new ExceptionWithData('some services parameter types are not supported',
            [
               'function' => $reflection_function->getName(),
               'filename' => $reflection_function->getFileName(),
               'line' => $reflection_function->getStartLine(),
               'exception_list' => $exception_list
            ]);
        }
        return $parameter_info_list;
    }


    public static function getParameterInfo(ReflectionParameter $parameter) : array {

        $name = $parameter->getName();

        $reflection_type = $parameter->getType();
        try {
            $type = is_null($reflection_type) ? 'string' : self::getParameterType($reflection_type);
        } catch ( ExceptionWithData $exception ) {
            throw new ExceptionWithData($exception->getMessage(), [
                'name' => $name
            ], $exception);
        }

        return [
            'name' => $name,
            'type' => $type
        ];
    }

    /**
     * Obtiene un tipo desde un tipo de un parámetro.
     * El resultado es un string que dice como se debe tratar dicha entrada o que se debe hacer con el o validar.
     * @param ReflectionType|null $type
     * @return string
     * @throws ExceptionWithData
     */
    public static function getParameterType(ReflectionType $type) : string {
        if ( $type instanceof ReflectionNamedType ) {
                 if ( $type->getName() === 'string') return 'string';
            else if ( $type->getName() === 'array') return 'array';
            else if ( $type->getName() === 'int' ) return 'int';
            else if ( $type->getName() === InputFile::class ) return InputFile::class;
            else if ( $type->getName() === InputFileList::class ) return InputFileList::class;
            else if ( $type->getName() === Request::class ) return Request::class;
            else throw new ExceptionWithData('service parameter type is not supported', [
                'type' => $type->getName()
            ]);
        } else {
            return 'string';
        }
    }

    /**
     * Obtiene un valor los valores de un parametro desde el request.
     * En concreto busca un valor de parametro con un nombre y lo trata de convertir a un tipo especifico.
     * Lanza excepciones si no se encuentra el parametro o si no corresponde al tipo especificado
     * @param Request $request
     * @param string $name
     * @param string $type
     * @return array|int|InputFile|InputFileList|Request|string
     * @throws ExceptionWithData
     */
    public static function getParameterValueFromRequest(Request $request, string $name, string $type) {
        if ( $type === 'array') {
            return $request->getArrayParameter($name);
        } else if ( $type === 'int' ) {
            return $request->getIntParameter($name);
        } else if ( $type === 'string' ) {
            return $request->getStringParameter($name);
        } else if ( $type == InputFile::class ) {
            return $request->getFileParameter($name);
        } else if ( $type == InputFileList::class ) {
            return $request->getFileListParameter($name);
        } else if ( $type === Request::class ) {
           return $request;
        } else {
            throw new ExceptionWithData('unsupported type in request', [
                'name' => $name,
                'type' => $type,
            ]);
        }
    }

    /**
     * Devuelve un arreglo con todos los parámetros  que se quieren recuperar.
     *
     * @param Request $request
     * @param array $parameter_info_list Debe cumplir con el formato establecido en {@see getParameterInfoList()}, es decir, se puede usar la salida directa de ese método o un array compatible con la salida.
     * @return array
     * @throws ExceptionWithData
     */
    public static function getParameterValueListFromRequest(Request $request, array $parameter_info_list) : array {
        $parameter_list = [];
        $exception_list = [];
        foreach ( $parameter_info_list as $parameter_info ) {
            try {
                $parameter_list[] = self::getParameterValueFromRequest($request, $parameter_info['name'], $parameter_info['type']);
            } catch ( Throwable $exception ) {
                $exception_list[] = $exception;
            }
        }

        if ( !empty($exception_list) ) {
            throw new ExceptionWithData('error obtaining parameter value list',
                [
                    'parameter_info_list' => $parameter_info_list,
                    'exception_list' => $exception_list
                ]);
        }
        return $parameter_list;

    }
}