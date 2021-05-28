# Wildfire Tribe Server-side JSON API
JSON API implementation based on https://jsonapi.org/format

**Quick Start Guide**  
Starting template:
```php
use Wildfire\Api\Api;
use Wildfire\Api\Request;
use Wildfire\Api\Response;

$api = new Api();

/**
 * handling a get request
 */
$api->get(function (Response $response) {
    $response->json('hello world')->send();
});

$api->errorNotFound();
```
