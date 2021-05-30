<?php
header('Content-Type: application/vnd.api+json');
echo json_encode(($api->getRequestBody()));
exit();
?>