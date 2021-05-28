<?php
namespace Wildfire\Api;

class Api {
    // the information server responds with to client
    private Response $response;

    // request made by client
    private Request $request;

    private $headers;

    public function __construct() {
        $this->response = new Response();
        $this->request = new Request();
        $this->headers = getallheaders();
    }

    /**
     * callback can only work with Response
     * @param $callback
     */
    public function get($callback) {
        if (!$this->isRequestMethod('get')) {
            return;
        }

        $callback($this->response);
        $this->close();
    }

    /**
     * callback can work with Response & Request
     * @param $callback
     */
    public function post($callback) {
        if (!$this->isRequestMethod('post')) {
            return;
        }

        $callback($this->response, $this->request);
        $this->close();
    }

    /**
     * callback can work with Response & Request
     * @param $callback
     */
    public function put($callback) {
        if (!$this->isRequestMethod('put')) {
            return;
        }

        $callback($this->response, $this->request);
        $this->close();
    }

    /**
     * callback can work with Response & Request
     * @param $callback
     */
    public function patch($callback) {
        if (!$this->isRequestMethod('patch')) {
            return;
        }

        $callback($this->response, $this->request);
        $this->close();
    }

    /**
     * callback can only work with Response
     * @param $callback
     */
    public function delete($callback) {
        if (!$this->isRequestMethod('delete')) {
            return;
        }

        $callback($this->response);
        $this->close();
    }

    /**
     * validates request method for API calls
     * @param string $reqMethod
     * @return bool
     */
    private function isRequestMethod(string $reqMethod): bool{
        $serverMethod = strtolower($_SERVER['REQUEST_METHOD']);
        $reqMethod = strtolower($reqMethod);

        return $serverMethod === $reqMethod;
    }

    /**
     * respond with 404 if requested route isn't found
     */
    public function errorNotFound() {
        http_response_code(404);
    }

    /**
     * close the api connection
     */
    private function close() {
        exit();
    }

    /*
    Servers MUST respond with a 415 Unsupported Media Type status code if a request specifies the header Content-Type: application/vnd.api+json with any media type parameters.
     */
    public function isValidJsonRequest() {
        $error = 0;

        if (is_array($this->headers['Content-Type']) && in_array('application/vnd.api+json', $this->headers['Content-Type'])) {
            //In some responses Content-type is an array
            $error = 1;

        } else if (strstr($this->headers['Content-Type'], 'application/vnd.api+json')) {
            $error = 1;
        }
        if ($error) {
            $this->response->send(415);
        } else {
            return true;
        }

    }

    /*
    This small helper function generates RFC 4122 compliant Version 4 UUIDs.
     */
    public function guidv4($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
