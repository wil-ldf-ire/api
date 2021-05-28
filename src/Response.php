<?php
namespace Wildfire\Api;

class Response {
    private $response;

    /**
     * writes data to response property
     * @param $response
     * @return $this
     */
    public function body($response): Response{
        $this->response = $response;
        return $this;
    }

    /**
     * converts response into a json object to be sent over network
     * @param $response
     * @return $this
     */
    public function json($response): Response{
        header('Content-Type: application/vnd.api+json');
        $this->response = json_encode($response);
        return $this;
    }

    /**
     * sets http code to response and responds to the request
     * @param int $status_code
     */
    public function send($status_code = 200) {
        http_response_code($status_code);
        $this->response['status'] = $status_code;

        if (!$this->response['id']) {
            $this->response['id'] = $this->guidv4();
        }

        if ($status_code == 415) {
            $this->response['title'] = 'Unsupported Media Type';
            $this->response['detail'] = 'Servers MUST respond with a 415 Unsupported Media Type status code if a request specifies the header Content-Type: application/vnd.api+json with any media type parameters.';
        }

        echo $this->response;
    }

    /*
    Servers MUST respond with a 415 Unsupported Media Type status code if a request specifies the header Content-Type: application/vnd.api+json with any media type parameters.
     */
    private function isValidJsonRequest() {

        $url_headers = getallheaders();
        $error = 0;

        if (is_array($url_headers['Content-Type']) && in_array('application/vnd.api+json', $url_headers['Content-Type'])) {
            //In some responses Content-type is an array
            $error = 1;

        } else if (strstr($url_headers['Content-Type'], 'application/vnd.api+json')) {
            $error = 1;
        }
        if ($error) {
            $this->send(415);
        } else {
            return true;
        }

    }

    /*
    This small helper function generates RFC 4122 compliant Version 4 UUIDs.
     */
    private function guidv4($data = null) {
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
