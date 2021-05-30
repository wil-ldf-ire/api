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
$currentUser = $auth->getCurrentUser(base64_decode($authHeader[1]));

if ($authHeader[0] == 'Bearer' && $currentUser['id']) {
    //if logged in and has bearer token, allow data access
    include_once __DIR__ . '/' . $api_version . '/data.php';
} else {
    //authenticate if not logged in
    include_once __DIR__ . '/' . $api_version . '/auth.php';
}