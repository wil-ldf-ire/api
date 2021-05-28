<?php
namespace Wildfire\Api;

class Request {
    // stores post body from json request
    private array $postBody;

    public function __construct() {
        // parse json from post body as associate array and store class property
        $this->postBody = json_decode(file_get_contents('php://input'), 1) ?? [];
    }

    public function postBody(): array
    {
        return $this->postBody;
    }
}
