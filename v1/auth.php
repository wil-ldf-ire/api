<?php
header('Content-Type: application/vnd.api+json');

$userpass = explode(':', base64_decode(explode(' ', $api->getRequestHeaders()['Authorization'])[1]));

$accessArray = $auth->getApiAccess($userpass[0], $userpass[1]);

if ($accessArray) {
    echo json_encode($accessArray);
} else {
    //Access denied
    $api->sendResponse(401);
}

exit();
?>