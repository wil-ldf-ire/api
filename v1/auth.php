<?php
header('Content-Type: application/vnd.api+json');

$api_auth_post_data=$api->getRequestBody();

$user_id = json_decode(
    $sql->executeSQL( "
        SELECT `content`->'$.user_id' `user_id` FROM `data`
        WHERE
        `content`->'$.api_key'='" . $api_auth_post_data['api_key'] . "' && 
        `content`->'$.api_secret'='" . $api_auth_post_data['api_secret'] . "' && 
        `content`->'$.type'='api_key_secret'")[0]['user_id'] );

echo json_encode($dash->get_user($user_id););
exit();
?>