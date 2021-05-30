<?php
namespace Wildfire\Api;

class Api {

    private $response = array();
    private $request = array();

    public function __construct() {

    }

    public function getRequestHeaders() {
        return getallheaders();
    }

    public function getRequestBody(): array
    {
        return json_decode(file_get_contents('php://input'), 1) ?? [];
    }

    /**
     * sets http code to response and responds to the request
     * @param int $status_code
     */
    public function sendResponse($status_code = 200) {
        http_response_code($status_code);
        $this->response['status'] = $status_code;

        if (!$this->response['id']) {
            $this->response['id'] = $this->guidv4();
        }

        if ($status_code == 200) {
            $this->response['title'] = 'OK';
            $this->response['detail'] = 'Successful.';
        } else if ($status_code == 415) {
            $this->response['title'] = 'Unsupported Media Type';
            $this->response['detail'] = 'Servers MUST respond with a 415 Unsupported Media Type status code if a request specifies the header Content-Type: application/vnd.api+json with any media type parameters.';
        } else if ($status_code == 400) {
            $this->response['title'] = 'Bad Request';
        } else if ($status_code == 401) {
            $this->response['title'] = 'Access Denied';
            $this->response['detail'] = 'Stop.';
        }

        echo json_encode($this->response);
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

    /*
    Servers MUST respond with a 415 Unsupported Media Type status code if a request specifies the header Content-Type: application/vnd.api+json with any media type parameters.
     */
    public function isValidJsonRequest() {
        $error = 0;
        $requestHeaders = $this->getRequestHeaders();

        if (is_array($requestHeaders['Content-Type']) && in_array('application/vnd.api+json', $requestHeaders['Content-Type'])) {
            //In some responses Content-type is an array
            $error = 1;

        } else if (strstr($requestHeaders['Content-Type'], 'application/vnd.api+json')) {
            $error = 1;
        }
        if ($error) {
            $this->sendResponse(415);
            die();
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
