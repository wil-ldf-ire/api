<?php
header('Content-Type: application/vnd.api+json');

$userpass = explode(':', base64_decode($authHeader[1]));
$accessArray = $auth->getApiAccess($userpass[0], $userpass[1]);

//If user has access, pass bearer token
if ($accessArray) {
    echo json_encode($accessArray);
}

//Access denied
else {
    $api->sendResponse(401);
}

exit();
?>