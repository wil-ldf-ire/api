# Wildfire Rest API
### Dependency for Wildfire\Core

This package helps in getting started with Rest APIs quickly without a lot of code;

**Quick Start Guide**  
Starting template:
```php
use Wildfire\RestAPI\Api;
use Wildfire\RestAPI\Request;
use Wildfire\RestAPI\Response;

$api = new Api();

/**
 * handling a get request
 */
$api->get(function (Response $response) {
    $response->json('hello world')->send();
});

$api->errorNotFound();
```
