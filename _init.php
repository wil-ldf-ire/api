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
$currentUser = $auth->getCurrentUser();

if (!$currentUser['id']) {
    $api->response->send(401);
} else {
    print_r($api->getRequestHeaders());
}