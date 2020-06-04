<?php
declare(strict_types=1);

namespace edwrodrig\hapi_core;

use edwrodrig\exception_with_data\ExceptionWithData;

/**
 * Class ServiceMap
 * @package edwrodrig\hapi_core
 */
class ServiceMap
{
    /**
     * @var array|callable[]
     */
    private array $service_map = [];

    /**
     * Crear un mapa de servicios.
     * El mapa de servicio se una clase que organiza los diferentes {@see Service servicios} en un solo lugar.
     * La principal función de esta clase es crear un {@see Service servicio} dado un nombre de método.
     * El nombre de método es un string que sirve para identificar un {@see Service servicio}.
     * Un servicio de debe registrar usando el {@see registerService()}.
     *
     * <strong>OJO:</strong>Solo se deben registrar servicios que tengan constructores que se puedan invocar </strong>sin parametros.</strong>
     *
     * <h2>Creación</h2>
     * Ejemplo:
     * <code>
     * $map = new ServiceMap;
     * $map
     *   ->registerService('get', ServiceGet::class)
     *   ->registerService('set', ServiceSet::class);
     * </code>
     *
     * <h2>Obtener servicio</h2>
     * Para obtener un servicio se debe usar el {@see getServiceByMethodName()}.
     * Ejemplo:
     * <code>
     * $service = $map->getServiceByMethodName('get');
     * $service->send();
     * </code>
     */
    public function __construct() {}

    /**
     * Registrar un servicio.
     * @param string $method_name
     * @param callback $service_callback
     * @return $this
     */
    public function registerService(string $method_name, $service_callback) : ServiceMap {
        $this->service_map[$method_name] = $service_callback;
        return $this;
    }

    /**
     * @param string $method_name
     * @return callable
     * @throws ExceptionWithData
     */
    public function getService(string $method_name) {
        if ( isset($this->service_map[$method_name]) ) {
            return $this->service_map[$method_name];
        } else {
            throw new ExceptionWithData("service not registered", ["method_name" => $method_name]);
        }
    }
}