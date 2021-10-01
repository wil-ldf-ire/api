<?php
namespace Wildfire;

class Api {

    private $response;
    private $request;
    private $requestBody;

    public function __construct()
    {
        $this->requestBody = json_decode(file_get_content('php://input'), 1) ?? [];
    }

    public function getRequestHeaders()
    {
        return getallheaders();
    }

    /**
     * returns the request body as an array
     */
    public function request(): array
    {
        return $this->requestBody;
    }

    /**
     * encodes passed data as a json that can be sent over network
     * @param any $data
     */
    public function json($data): Api
    {
        $this->response = json_encode($data);
        return $this;
    }

    /**
     * sets http code to response and responds to the request
     * @param int $status_code
     */
    public function send($status_code = 200)
    {
        // set header and status code
        header('Content-Type: application/vnd.api+json');
        http_response_code($status_code);

        echo json_encode($this->response);
    }

    /**
     * validates request method for API calls
     * @param string $reqMethod
     * @return bool
     */
    private function isRequestMethod(string $reqMethod): bool
    {
        $serverMethod = strtolower($_SERVER['REQUEST_METHOD']);
        $reqMethod = strtolower($reqMethod);

        return $serverMethod === $reqMethod;
    }

    /*
     * Servers MUST respond with a 415 Unsupported Media Type status code
     * if a request specifies the header Content-Type: application/vnd.api+json
     * with any media type parameters.
     */
    public function isValidJsonRequest()
    {
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
     * This small helper function generates RFC 4122 compliant Version 4 UUIDs.
     */
    public function guidv4($data = null)
    {
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
