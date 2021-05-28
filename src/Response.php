<?php
namespace Wildfire\Api;

class Response {
    private $response;
    private Api $api;
    private Request $request;

    public function __construct() {
        $this->api = new Api();
        $this->request = new Request();
    }

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
            $this->response['id'] = $this->api->guidv4();
        }

        if ($status_code == 415) {
            $this->response['title'] = 'Unsupported Media Type';
            $this->response['detail'] = 'Servers MUST respond with a 415 Unsupported Media Type status code if a request specifies the header Content-Type: application/vnd.api+json with any media type parameters.';
        }

        echo $this->response;
    }
}
