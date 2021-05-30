<?php
header('Content-Type: application/vnd.api+json');

//If user has access
if ($currentUser['id']) {
    echo json_encode(array('data' => 'access'));
}

//Access denied
else {
    $api->sendResponse(401);
}

exit();
?>