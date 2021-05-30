<?php
namespace Wildfire;

$sql = new Core\MySQL();
$dash = new Core\Dash();
$admin = new Core\Admin();
$auth = new Auth\Auth();
$api = new Api\Api();

$type = 'api';
$types = $dash->getTypes();
$menus = $dash->getMenus();

$api_version = explode('/', $_SERVER['REQUEST_URI'])[2];

$authHeader = explode(' ', $api->getRequestHeaders()['Authorization']);

//if logged in and has bearer token, allow data access
if ($authHeader[0] == 'Bearer' && ($access_token = $authHeader[1])) {
    $currentUser = $auth->getCurrentUser($access_token);
    include_once __DIR__ . '/' . $api_version . '/data.php';
}

//authenticate if not logged in
else if ($authHeader[0] == 'Basic' && ($userpass = explode(':', base64_decode($authHeader[1])))) {
    include_once __DIR__ . '/' . $api_version . '/auth.php';
}

//Access denied
else {
    $api->sendResponse(401);
}