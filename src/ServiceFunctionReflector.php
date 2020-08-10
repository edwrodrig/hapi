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

    /*
        public static function getParameterList(\ReflectionFunctionAbstract $reflection_function) {
            $reflection_parameter_list = $reflection_function->getParameters();
            array_map(function(\ReflectionParameter $parameter) {
                'name' => $reflection_parameter->getName();
                $reflection_parameter->getType()->getName();
            })
            foreach ( $reflection_parameter_list as $reflection_parameter ) {
                $reflection_parameter->getName();
                $reflection_parameter->getType();

            }
        }
    */

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
            else if ( $type->getName() === 'bool' ) return 'bool';
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

    public static function getParameterForRequest(Request $request, string $name, string $type) {
        if ( $type === 'array') {
            if ( isset($request->getParameterList()['name'] ))
                $request->getParameter($name);
            else {

            }
        } else if ( $type === Request::class ) {
            return $request;
        } else {
            $request->getParameter($name);
        }
    }
}