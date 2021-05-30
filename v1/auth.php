<?php
header('Content-Type: application/vnd.api+json');
print_r($api->getRequestHeaders());
print_r($api->getRequestBody());
exit();
?>