<?php
include_once __DIR__ . '/_init.php';

if (!$currentUser['id']) {
    $api->response->send(401);
} else {
    print_r($api->request->getHeaders());
}