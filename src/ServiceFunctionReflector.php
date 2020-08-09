<?php
declare(strict_types=1);

namespace labo86\hapi;


use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi_core\Request;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;

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

    public static function getParameterInfo(ReflectionParameter $parameter) : array {

        $name = $parameter->getName();

        $reflection_type = $parameter->getType();
        $type = is_null($reflection_type) ? 'string' : self::getParameterType($reflection_type);

        return [
            'name' => $name,
            'type' => $type
        ];
    }

    /**
     * Obtiene un tipo desde un
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
            else if ( $type->getName() === Request::class ) return Request::class;
            else throw new ExceptionWithData('service parameter type is unsupported', [
                'type' => $type->getName()
            ]);
        } else {
            return 'string';
        }
    }
}