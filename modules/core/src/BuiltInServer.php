<?php
declare(strict_types=1);

namespace edwrodrig\hapi_core;

/**
 * Class BuiltInServer
 * Esta clase sirve para lanzar un servidor PHP local para pruebas.
 * @package test\edwrodrig\hapi_core
 */
class BuiltInServer
{
    /**
     * @var resource
     */
    private $server_process;

    private string $document_root;

    private int $port = 2280;

    /**
     * Construir un BuiltInServer
     *
     * Esta clase esta pensada solo para usarse en modo de pruebas ya que lanzar un servidor PHP es algo truculento y propenso a bugs.
     * Esta clase es solo una interfaz para el {@see https://www.php.net/manual/en/features.commandline.webserver.php Servidor PHP Interno}.
     * Por el momento solo es compatible con Linux porque usa el comando {@see https://www.man7.org/linux/man-pages/man1/kill.1.html <code>kill</code>}
     *
     * <h2>Creación y puesta en marcha</h2>
     * Para poner en marcha provea un la raíz de los documentos y {@see run()} para poner en marcha el servidor.
     * <code>
     * $server = new BuildInServer('/home/user/www');
     * $server->run();
     * </code>
     * <h2>Request</h2>
     * Se utiliza {@see makeRequest()} para hacer peticiones al servidor
     * Ejemplo:
     * <code>
     * $response = $service->makeRequest('index.html');
     * </code>

     * @param string $document_root la carpeta que es la {@see https://www.php.net/manual/en/reserved.variables.server.php raíz de los documentos}
     */
    public function __construct(string $document_root) {
        if ( !is_dir($document_root) ) $document_root = '.';
        $this->document_root = $document_root;
    }

    /**
     * Función complicada.
     * Puede ser propensa a errores.
     * Funciona pero no confío mucho en ella. Si ocurren errores puede que acá esté la causa.
     */
    public function __destruct()
    {
        if ( is_resource($this->server_process) ) {
            $status = proc_get_status($this->server_process);

            if (  $status["running"] )
                exec("kill -9 " . $status['pid']);

            while ( proc_get_status($this->server_process)["running"]  ) {
                sleep(1);
            }
            proc_close($this->server_process);
        }
    }

    /**
     * Lanza el servidor
     *
     * Esta función usa {@see proc_open()} para lanzar el servidor.
     * El servidor siempre se lanza en el puerto <strong>2280</strong>.
     * Espero que sea un número lo suficientemente extraño como para no tener problemas en ambientes de prueba.
     *
     * Se pueden hacer request con {@see makeRequest()}.
     * Con {@see getCommand()} se puede ver el comando que es ejecutado para levantar el servidor.
     * @return bool
     */
    public function run() : bool {
        $command = $this->getCommand();
        $this->server_process = proc_open($command, [
            0 => ['pipe', 'r'],  // stdin is a pipe that the child will read from
            1 => ['pipe', 'w'],  // stdout is a pipe that the child will write to
            2 => ['pipe', 'w'] // stderr is a pipe that the child will write to], $pipes);
        ], $pipes);
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        if ( is_resource($this->server_process) ) {
            sleep(1);
            return true;
        }
        return false;
    }

    /**
     * Ver el comando que se ejecuta para levantar el servidor.
     * Lo que hace por el momento es escuchar el <strong>localhost</strong> en el puerto <strong>2280</strong>.
     * @return string
     */
    public function getCommand() : string {
        return sprintf("exec php -S localhost:%d -t %s", $this->port, escapeshellarg($this->document_root));
    }

    /**
     * Se obtiene la URL base del servidor.
     * Se usa para construir URLs de peticiones del servidor.
     * @see makeRequest() Es más fácil hacer request con esta función
     * @return string
     */
    public function getBaseUrl() : string {
        return sprintf("http://localhost:%d", $this->port);
    }

    /**
     * Función de conveniencia para hacer peticiones al servidor. Se debe haber llamado a {@see run()} previamente}.
     * Internamente concatena la {@see getBaseUrl() la URL base} con el archivo para hacer una petición usando {@see file_get_contents()}.
     *
     * <h2>Request simple</h2>
     * Ejemplo:
     * <code>
     * $server->makeRequest('index.html');
     * </code>
     *
     * <h2>Request con POST</h2>
     * Si se quiere hacer una request por el método POST, hacer algo como lo siguiente:
     * <code>
     * $context  = stream_context_create([
     *    'http' => [
     *      'header'  => "Content-type: text/plain\r\n",
     *      'method'  => 'POST',
     *      'content' => 'hello world'
     *    ]
     * ]);
     * $server->makeRequest('index.php', $context);
     * </code>
     * @param string $file ruta del archivo, puede tener {@see https://tools.ietf.org/html/rfc3986#section-3.4 query string}
     * @param resource|null $context el {@see https://www.php.net/manual/en/function.stream-context-create.php stream context} correspondiente a {@see file_get_contents()}
     * @return string
     */
    public function makeRequest(string $file, $context = null) : string {
        $url = $this->getBaseUrl() . '/' . $file;
        return file_get_contents($url, false, $context);
    }

}