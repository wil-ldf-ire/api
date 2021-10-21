<?php
/*
 * This file is supposed to be called from \Wildfire\Api\Api and thus depends on
 * properties and functions of class Api. Please reference the Api class for
 * more clarity on this files behavior
 */

switch (strtolower($_SERVER['REQUEST_METHOD'])) {
    case 'get':
        fetch($this, $url_parts, $all_types);
        break;

    case 'post':
        create($this, $url_parts, $all_types);
        break;

    case 'put':
        update($this, $url_parts, $all_types);
        break;

    case 'delete':
        delete($this, $url_parts, $all_types);
        break;

    default:
        break;
}

function fetch($api, $url_parts, $all_types) {
    /////////////////////////
    // fetch records by id //
    /////////////////////////
    if (is_numeric($url_parts[1])) {
        $res = $api->findById($url_parts[1]);

        if (!$res) {
            $api->json([ "error" => "id not found" ])->send(404);
        }

        if (isset($url_parts[2])) {
            cherryPick($url_parts[2], $res);
        }

        $api->json($res)->send();
    }

    ////////////////////////////////////////////////////////////////////////////
    // if $url_parts[1] is a valid and defined 'type' and no 'slug' mentioned //
    ////////////////////////////////////////////////////////////////////////////
    if (!$url_parts[2] && in_array($url_parts[1], $all_types)) {
        $res = $api->findByType($url_parts[1], $db_index, $db_limit);

        if (!$res) {
            $api->json([ "error" => "type not found" ])->send(400);
        }

        $res = [
            "index" => $db_index,
            "limit" => $db_limit,
            "data" => $res
        ];

        $api->json($res)->send();
    }

    /////////////////////////////////////////////////////////////////////////
    // if $url_parts[1] is a valid and defined 'type' and 'slug' mentioned //
    /////////////////////////////////////////////////////////////////////////
    if ($url_parts[2] && in_array($url_parts[1], $all_types)) {
        $res = $api->findBySlug($url_parts[1], $url_parts[2]);

        if(!$res) {
            $api->json([ "error" => "type and slug match not found" ])->send(404);
        }

        if (isset($url_parts[3])) {
            cherryPick($url_parts[3], $res);
        }

        $api->json($res)->send();
    }

    $api->json([ 'error' => 'not found' ])->send(404);
}

function create($api, $url_parts, $all_types) {
    $dash = new Wildfire\Core\Dash;

    $type = $url_parts[1] ?? null;

    if (!($type || in_array($type, $all_types))) {
        $api->json([ 'error' => 'not found' ])->send(404);
    }

    $req = $api->body();
    $type_arr = [ "type" => $type ];

    if ($type == 'user') {
        if ($req['password'] !== $req['confirm_password']) {
            $api->json(['error' => 'passwords do not match'])->send(401);
        }

        $type_arr['user_id'] = $dash->get_unique_user_id();
        unset($req['confirm_password']);
    }

    $req = array_merge($req, $type_arr);
    $res = $dash->push_content($req);

    $api->json($res)->send();
}

function update($api, $url_parts, $all_types) {
    $dash = new \Wildfire\Core\Dash;

    if (!is_numeric($url_parts[1]) && !$url_parts[2]) {
        $api->json(['error' => 'slug not mentioned'])->send(400);
    }

    if (is_string($url_parts[1]) && $url_parts[2]) {
        $id = (int)$dash->get_content_meta(array('type' => $url_parts[1], 'slug' => $url_parts[2]), 'id');
    }

    if (is_numeric($url_parts[1])) {
        $id = (int)$url_parts[1];
    }

    $req = $api->body();

    /**
     * ToDo: password hashing still needs to be handled
     */
    if ($req['password']) {
        if ($req['password'] !== $req['confirm_password']) {
            $api->json(['error' => "passwords don't match"])->send(400);
        }
    }

    foreach ($req as $key => $value) {
        $status = $dash->push_content_meta($id, $key, $value);

        if (!$status) {
            $api->json(['error' => 'something went wrong'])->send(500);
        }
    }

    $res = $dash->get_content($id);

    $api->json($res)->send();
}

function delete($api, $url_parts, $all_types) {
    $dash = new Wildfire\Core\Dash;

    if (is_numeric($url_parts[1])) {
        $id = (int)$url_parts[1];

        $res_0 = $dash->get_content($id);

        if (!$res_0) {
            $api->json(['error' => 'id invalid'])->send(404);
        }

        $dash->do_delete(['id' => $id]);
        $res_1 = $dash->get_content($id);

        if ($res_0 && !$res_1) {
            $api->json(['success' => true])->send();
        }

        $api->json(['error' => 'something went wrong'])->send(503);
    }

    if (!$url_parts[2] || !in_array($url_parts[1], $all_types)) {
        $api->json(['error' => 'invalid request'])->send(400);
    }

    $res = $dash->get_content(['type' => $url_parts[1], 'slug' => $url_parts[2] ]);

    if (!$res) {
        $api->json(['error' => 'record not found'])->send(404);
    }

    $dash->do_delete([
        'id' => $res['id']
    ]);

    $api->json(['success' => 'true'])->send();
}

function cherryPick($needle, $haystack) {
    if (!$needle && !haystack) {
        return false;
    }

    $api = new \Wildfire\Api\Api;

    if (isset($haystack[$needle])) {
        $api
            ->json([
                $needle => $haystack[$needle]
            ])
            ->send();
    }

    $api
        ->json([
            "error" => "'{$needle}' is not defined"
        ])
        ->send();
}
