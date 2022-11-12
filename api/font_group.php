<?php

include_once '../util/init.php';
include_once '../models/Font.php';
include_once '../models/FontGroup.php';
include_once '../models/FontForFontGroup.php';

switch($REQUEST_METHOD) {
    
    case "POST":
        $response = createFontGroup($REQUEST_BODY_JSON, $db);
        break;

    case "GET":
        $response = isset($URL_PARAMS['id']) ? readFontGroup($URL_PARAMS['id'], $db) : readAllFontGroups($db);
        break;

    case "PUT":
        $response = isset($URL_PARAMS['id']) ? updateFontGroup($URL_PARAMS['id'], $REQUEST_BODY_JSON, $db) : no_id_in_query_param_bad_request();
        break;

    case "DELETE":
        $response = isset($URL_PARAMS['id']) ? deleteFontGroup($URL_PARAMS['id'], $db) : no_id_in_query_param_bad_request();
        break;

    default:
        $response = request_method_not_allowed($REQUEST_METHOD);

}

echo json_encode($response);

function createFontGroup($data, $db) {
    try {
        $fontGroup = new FontGroup($db);
        $fontGroup->fontGroupName = $data->fontGroupName;
        if (count($data->selectedFontList) < 2) {
            return validation_fail_response("atleast 2 fonts required", 422);
        } else if (duplicate_fontNames_exist($data->selectedFontList)) {
            return validation_fail_response("fonts must be unique", 422);
        }
        foreach($data->selectedFontList as $f) {
            $fontForFontGroup = new FontForFontGroup($db);
            $fontForFontGroup->fontName = $f->fontName;
            $fontForFontGroup->selectedFontId = $f->selectedFontId;
            $fontForFontGroup->specificSize = $f->specificSize;
            $fontForFontGroup->priceChange = $f->priceChange;
            array_push($fontGroup->selectedFontList, $fontForFontGroup);
        }
        $success = $fontGroup->create();
        if (!$success) {
            throw new Exception("failed to save fontGroup in db");
        }
        return array(
            "status" => "success",
            "error" => false,
            "message" => "font group created successfully"
        );
    } catch (PDOException $pe) {
        return data_constraint_violation_response($pe);
    } catch (Exception $e) {
        return server_side_exception_response($e);
    }
}

function readFontGroup($fontGroupId, $db) {
    try {
        $fontGroup = new FontGroup($db);
        $fontGroup = $fontGroup->read($fontGroupId);
        if ($fontGroup==null) {
            return not_found_response("font group with id: ". $fontGroupId ." not found");
        }
        return $fontGroup;
    } catch (Exception $e) {
        return server_side_exception_response($e);
    }
}

function readAllFontGroups($db) {
    try {
        $fontGroup = new FontGroup($db);
        return $fontGroup->readAll();
    } catch (Exception $e) {
        return server_side_exception_response($e);
    }
}

function updateFontGroup($fontGroupId, $data, $db) {
    return $data;
}

function deleteFontGroup($fontGroupId, $db) {
    return array();
}

function duplicate_fontNames_exist($fontForGroupList) {
    $size = count($fontForGroupList);
    for($i = 0; $i < $size; $i++) {
        for($j = $i+1; $j < $size; $j++) {
            if ($fontForGroupList[$i]->fontName == $fontForGroupList[$j]->fontName) {
                return true;
            }
        }
    }
    return false;
}

?>