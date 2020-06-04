<?php
declare(strict_types=1);

namespace edwrodrig\hapi;

use edwrodrig\exception_with_data\ExceptionWithData;
use Throwable;

class ServiceException extends ExceptionWithData {

    public static function getDataForDeveloper (Throwable $throwable) : array {
        if ( $throwable instanceof ServiceException ) {
            $data = $throwable->getPrevious() ? self::getDataForDeveloper($throwable->getPrevious()) : [];
            $data['u'] = self::getDataForUser($throwable);
            return $data;
        }
        else if ( $throwable instanceof ExceptionWithData ) {
            /** @var $throwable ExceptionWithData */

            return [
                'm' => $throwable->getMessage(),
                'd' => $throwable->getData(),
                'f' => [$throwable->getFile(),$throwable->getLine()]
            ];

        } else {
            return  [
                'm' => $throwable->getMessage(),
                'f' => [$throwable->getFile(),$throwable->getLine()],
                't' => $throwable->getTrace()
            ];
        }
    }

    public static function getDataForUser(Throwable $throwable) : array {
        if ( $throwable instanceof ServiceException ) {
            /** @var $throwable ServiceException */
            return [
                'm' => $throwable->getMessage(),
                'd' => $throwable->getData()
            ];
        } else {
            return [
                'm' => 'internal error',
                'd' => []
            ];
        }
    }
}