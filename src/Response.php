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
        echo $this->response;
    }
}
