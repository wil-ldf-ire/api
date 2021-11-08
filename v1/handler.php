<?php
/*
 * This file is supposed to be called from \Wildfire\Api\Api and thus depends on
 * properties and functions of class Api. Please reference the Api class for
 * more clarity on this files behavior
 */
use alsvanzelf\jsonapi\ErrorsDocument;
use alsvanzelf\jsonapi\ResourceDocument;
use alsvanzelf\jsonapi\CollectionDocument;
use alsvanzelf\jsonapi\objects\ResourceObject;

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
    try {
        $check_use_id = false;  // default value will be overriden if required

        if (is_numeric($url_parts[0])) {    // if request has only numeric id
            $check_use_id = true;
            $use_id = true;
        } else if ($url_parts[1] && in_array($url_parts[0], $all_types)) {  // valid type and slug
            $check_use_id = true;
            $use_id = false;
        }

        if ($check_use_id) {
            if ($use_id) {
                $res = $api->findById($url_parts[0]);
            } else {
                $res = $api->findBySlug($url_parts[0], $url_parts[1]);
            }

            if (!$res) {    // send 404 message
                throw new Exception('unknown id', 404);
            }

            $doc = new ResourceDocument($type=$res['type'], $id=$res['id']);
            unset($res['type'], $res['id']);

            if (isset($_GET['filter'])) {
                $filter = explode(',', $_GET['filter']);

                foreach($filter as $f) {
                    $doc->add($f, $res[$f]);
                }
            } else {
                foreach($res as $key => $value) {
                    $doc->add($key, $value);
                }
            }

            $doc->sendResponse();
            die();
        }

        /**
         * $url_parts[0]: valid
         * type: defined & valid
         * slug: undefined
         */
        if (!$url_parts[1] && in_array($url_parts[0], $all_types)) {
            $db_index = $_GET['index'] ?? 0;
            $db_limit = $_GET['limit'] ?? 20;

            $res = $api->findByType($url_parts[0], $db_index, $db_limit);

            if (!$res) {
                throw new Exception('unknown id', 404);
            }

            foreach($res as $r) {
                $temp = new ResourceObject($r['type'], $r['id']);

                if (isset($_GET['filter'])) {
                    $filters = explode(',', $_GET['filter']);

                    foreach($filters as $filter) {
                        $temp->add($filter, $r[$filter]);
                    }
                } else {
                    unset($r['id'], $r['type']);

                    foreach($r as $key => $value) {
                        $temp->add($key, $value);
                    }
                }

                $final_object[] = $temp;
            }

            $doc = CollectionDocument::fromResources(...$final_object);
            $doc->sendResponse();
            die();
        }

        // default error
        throw new Exception('unknown request', 404);
    } catch (Exception $e) {
        $options = [
            'includeExceptionTrace'    => false,
            'includeExceptionPrevious' => false,
        ];
        $document = ErrorsDocument::fromException($e, $options);

        $options = [
            'prettyPrint' => true,
        ];
        echo $document->sendResponse();
        die();
    }
}

function create($api, $url_parts, $all_types) {
    $dash = new Wildfire\Core\Dash;

    $type = $url_parts[0] ?? null;

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

    if (!is_numeric($url_parts[0]) && !$url_parts[1]) {
        $api->json(['error' => 'slug not mentioned'])->send(400);
    }

    if (is_string($url_parts[0]) && $url_parts[1]) {
        $id = (int)$dash->get_content_meta(array('type' => $url_parts[0], 'slug' => $url_parts[1]), 'id');
    }

    if (is_numeric($url_parts[0])) {
        $id = (int)$url_parts[0];
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

    if (is_numeric($url_parts[0])) {
        $id = (int)$url_parts[0];

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

    if (!$url_parts[1] || !in_array($url_parts[0], $all_types)) {
        $api->json(['error' => 'invalid request'])->send(400);
    }

    $res = $dash->get_content(['type' => $url_parts[0], 'slug' => $url_parts[1] ]);

    if (!$res) {
        $api->json(['error' => 'record not found'])->send(404);
    }

    $dash->do_delete([
        'id' => $res['id']
    ]);

    $api->json(['success' => 'true'])->send();
}
