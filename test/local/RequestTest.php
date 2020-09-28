<?php
declare(strict_types=1);

namespace test\labo86\hapi\local;

use labo86\exception_with_data\ExceptionWithData;
use labo86\hapi\BuiltInServer;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    protected static BuiltInServer $server;

    public static function setUpBeforeClass(): void
    {
        self::$server = new BuiltInServer(__DIR__ . '/resources/www');
        self::$server->run();
    }

    public static function tearDownAfterClass(): void
    {
        self::$server->__destruct();
    }

    public function assertJsonStringToArray(array $expected, string $actual, string $message = "") {
        $this->assertJson($actual);
        $this->assertEquals($expected, json_decode($actual, true), $message);
    }

    public function getMethodProvider()
    {
        return [
            [[], '{"m":"request does not have parameter","d":{"parameter_name":"method","available_parameter_list":[]}}', ""],
            [["method" => "action"], "action", "?method=action"],
            [["param" => "action"], '{"m":"request does not have parameter","d":{"parameter_name":"method","available_parameter_list":{"param":"action"}}}', "?param=action"],
            [["method" => "action", "param" => "content"], "action", "?method=action&param=content"]
        ];
    }

    /**
     * @dataProvider getMethodProvider
     * @param array $expected_params
     * @param string $expected_method
     * @param string $query_params
     * @throws ExceptionWithData
     */
    public function testGetRequest(array $expected_params, string $expected_method, string $query_params)
    {
        $response = self::$server->makeRequest('get_method.php' . $query_params);
        $this->assertEquals($expected_method, $response);

        $response = self::$server->makeRequest('get_params.php' . $query_params);
        $this->assertJsonStringToArray($expected_params, $response);
    }

    /**
     * @dataProvider getMethodProvider
     * @throws ExceptionWithData
     */
    public function testJsonRequest()
    {
        $context  = [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode(["method" => "action", "param" => "content"])
        ];


        $response = self::$server->makeRequest('get_method.php', $context);
        $this->assertEquals("action", $response);

        $response = self::$server->makeRequest('get_params.php', $context);
        $this->assertJsonStringToArray(["method" => "action", "param" => "content"], $response);
    }

    /**
     * @dataProvider getMethodProvider
     * @throws ExceptionWithData
     */
    public function testJsonRequestCharset()
    {
        $context  = [
                'header'  => "Content-type: application/json; charset=utf-8\r\n",
                'method'  => 'POST',
                'content' => json_encode(["method" => "action", "param" => "content"])
        ];

        $response = self::$server->makeRequest('get_method.php', $context);
        $this->assertEquals("action", $response);

        $response = self::$server->makeRequest('get_params.php', $context);
        $this->assertJsonStringToArray(["method" => "action", "param" => "content"], $response);
    }

    /**
     * @throws ExceptionWithData
     */
    public function testJsonRequestNotAnArray()
    {
        $context  = [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode("hola")
        ];


        $response = self::$server->makeRequest('get_method.php', $context);

        $this->assertJsonStringToArray(['m' => 'json in post is not an array', 'd' => ['contents' => '"hola"']], $response);

    }

    /**
     * @throws ExceptionWithData
     */
    public function testJsonRequestNotAValidJson()
    {
        $context  = [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => '{id}'
        ];


        $response = self::$server->makeRequest('get_method.php', $context);

        $json_response = json_decode($response, true);
        $this->assertEquals(['contents' => '{id}'], $json_response['d']);
        $this->assertJsonStringToArray(['m' => 'post content is not a valid json', 'd' => ['contents' => '{id}']], $response);

    }

    /**
     * @see https://www.w3.org/TR/html401/interact/forms.html#h-17.13.4.2
     * @throws ExceptionWithData
     */
    public function testMultipartRequestMethod()
    {
        $context  = [
            'header'  => "Content-Type: multipart/form-data; boundary=BOUNDARY\r\n",
            'method'  => 'POST',
            'content' =>
"--BOUNDARY\r\n" .
"Content-Disposition: form-data; name=\"method\"\r\n" .
"\r\n" .
"action\r\n" .
"--BOUNDARY\r\n" .
"Content-Disposition: form-data; name=\"filename\"\r\n" .
"Content-Type: application/xml;version=1.0;charset=UTF-8\r\n" .
"\r\n" .
"<xml>content</xml>\r\n" .
"--BOUNDARY--\r\n"
        ];


        $response = self::$server->makeRequest('get_method.php', $context);
        $this->assertEquals("action", $response);

    }

    /**
     * @see https://www.w3.org/TR/html401/interact/forms.html#h-17.13.4.2
     * @throws ExceptionWithData
     */
    public function testMultipartRequestMethod2()
    {
        $context  = [
            'header'  => "Content-Type: multipart/form-data; boundary=BOUNDARY\r\n",
            'method'  => 'POST',
            'content' => <<<EOF
--BOUNDARY
Content-Disposition: form-data; name="method"

action
--BOUNDARY--
EOF
        ];


        $response = self::$server->makeRequest('get_method.php', $context);
        $this->assertEquals("action", $response);

    }

    /**
     * @see https://www.w3.org/TR/html401/interact/forms.html#h-17.13.4.2
     * @throws ExceptionWithData
     */
    public function testMultipartRequestFile()
    {
        $context  = [
            'header'  => "Content-Type: multipart/form-data; boundary=BOUNDARY\r\n",
            'method'  => 'POST',
            'content' => <<<EOF
--BOUNDARY
Content-Disposition: form-data; name="method"

action
--BOUNDARY
Content-Disposition: form-data; name="file"; filename="something.txt"
Content-Type: text/plain

hello world
--BOUNDARY--
EOF
        ];


        $response = self::$server->makeRequest('get_file.php', $context);
        $json_data = json_decode($response, true);
        $this->assertArrayHasKey('file', $json_data);
        $file_data = $json_data['file'];
        $this->assertArrayHasKey('tmp_name', $file_data);
        unset($file_data['tmp_name']);
        $this->assertEquals([
            'name' => 'something.txt',
            'type' => 'text/plain',
            'size' => 11,
            'error' => 0]
            ,$file_data);

    }

    /**
     * Cuando no tiene filename, no usa el filename
     * @throws ExceptionWithData
     */
    public function testMultipartRequestFileWithoutFilename()
    {
        $context  = [
            'header'  => "Content-Type: multipart/form-data; boundary=BOUNDARY\r\n",
            'method'  => 'POST',
            'content' => <<<EOF
--BOUNDARY
Content-Disposition: form-data; name="method"

action
--BOUNDARY
Content-Disposition: form-data; name="file"
Content-Type: text/plain

hello world
--BOUNDARY--
EOF
        ];


        $response = self::$server->makeRequest('get_file.php', $context);
        $json_data = json_decode($response, true);
        $this->assertArrayHasKey('file', $json_data);
        $file_data = $json_data['file'];
        $this->assertArrayNotHasKey('tmp_name', $file_data);
        $this->assertEquals([
            'm' => 'request does not have parameter',
            'd' => [
                'parameter_name' => 'file',
                'available_parameter_list' => []
            ]]
            ,$file_data);

    }

    /**
     * Sen prueba el envio con un archivo
     * @throws ExceptionWithData
     */
    public function testMultipartRequestFileListOnlyOne()
    {
        $context  = [
            'header'  => "Content-Type: multipart/form-data; boundary=BOUNDARY\r\n",
            'method'  => 'POST',
            'content' => <<<EOF
--BOUNDARY
Content-Disposition: form-data; name="method"

action
--BOUNDARY
Content-Disposition: form-data; name="file_list[]"; filename="something.txt"
Content-Type: text/plain

hello world
--BOUNDARY--
EOF
        ];


        $response = self::$server->makeRequest('get_file.php', $context);
        $json_data = json_decode($response, true);
        $this->assertArrayHasKey('file_list', $json_data);
        $file_list_data = $json_data['file_list'];
        $this->assertCount(1, $file_list_data);
        $file_data = $file_list_data[0];
        $this->assertArrayHasKey('tmp_name', $file_data);
        unset($file_data['tmp_name']);
        $this->assertEquals([
                'name' => 'something.txt',
                'type' => 'text/plain',
                'size' => 11,
                'error' => 0]
            ,$file_data);

    }

    /**
     * Sen prueba el envio con un archivo
     * @throws ExceptionWithData
     */
    public function testMultipartRequestFileListMultipleOne()
    {
        $context  = [
            'header'  => "Content-Type: multipart/form-data; boundary=BOUNDARY\r\n",
            'method'  => 'POST',
            'content' => <<<EOF
--BOUNDARY
Content-Disposition: form-data; name="method"

action
--BOUNDARY
Content-Disposition: form-data; name="file_list[]"; filename="something.txt"
Content-Type: text/plain

hello world
--BOUNDARY
Content-Disposition: form-data; name="file_list[]"; filename="other.txt"
Content-Type: text/plain

hello world!
--BOUNDARY
Content-Disposition: form-data; name="file_list[]"; filename="other2.txt"
Content-Type: text/plain

hello world!!
--BOUNDARY--
EOF
        ];


        $response = self::$server->makeRequest('get_file.php', $context);
        $json_data = json_decode($response, true);
        $this->assertArrayHasKey('file_list', $json_data);
        $file_list_data = $json_data['file_list'];
        $this->assertCount(3, $file_list_data);
        $file_data = $file_list_data[0];
        $this->assertArrayHasKey('tmp_name', $file_data);
        unset($file_data['tmp_name']);
        $this->assertEquals([
                'name' => 'something.txt',
                'type' => 'text/plain',
                'size' => 11,
                'error' => 0]
            ,$file_data);

        $file_data = $file_list_data[1];
        $this->assertArrayHasKey('tmp_name', $file_data);
        unset($file_data['tmp_name']);
        $this->assertEquals([
                'name' => 'other.txt',
                'type' => 'text/plain',
                'size' => 12,
                'error' => 0]
            ,$file_data);

        $file_data = $file_list_data[2];
        $this->assertArrayHasKey('tmp_name', $file_data);
        unset($file_data['tmp_name']);
        $this->assertEquals([
                'name' => 'other2.txt',
                'type' => 'text/plain',
                'size' => 13,
                'error' => 0]
            ,$file_data);

    }

}
