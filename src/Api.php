<?php
namespace Wildfire\RestAPI;

class Api {
    // the information server responds with to client
    private Response $response;

    // request made by client
    private Request $request;

    public function __construct()
    {
        $this->response = new Response();
        $this->request = new Request();
    }

    /**
     * callback can only work with Response
     * @param $callback
     */
    public function get($callback)
    {
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
    public function post($callback)
    {
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
    public function put($callback)
    {
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
    public function patch($callback)
    {
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
    public function delete($callback)
    {
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
    private function isRequestMethod(string $reqMethod): bool
    {
        $serverMethod = strtolower($_SERVER['REQUEST_METHOD']);
        $reqMethod = strtolower($reqMethod);

        return $serverMethod === $reqMethod;
    }

    /**
     * respond with 404 if requested route isn't found
     */
    public function errorNotFound()
    {
        http_response_code(404);
    }

    /**
     * close the api connection
     */
    private function close()
    {
        exit();
    }
}
