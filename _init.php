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
    $url_words = explode('/', $_SERVER['REQUEST_URI']);
    include_once __DIR__ . '/' . $url_words[2] . '/auth.php';
    //$api->response->send(401);
} else {
    $url_words = explode('/', $_SERVER['REQUEST_URI']);
    include_once __DIR__ . '/' . $url_words[2] . '/auth.php';
}