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

echo json_encode($response, JSON_UNESCAPED_SLASHES);

function createFontGroup($data, $db) {
    try {
        $fontGroup = new FontGroup($db);
        $fontGroup->fontGroupName = $data->fontGroupName;
        if (count($data->selectedFontList) < 2) {
            return validation_fail_response("atleast 2 fonts required", 422);
        } else if (duplicateFontsExist($data->selectedFontList)) {
            return validation_fail_response("fonts must be unique", 422);
        }
        foreach($data->selectedFontList as $f) {
            $fontForFontGroup = new FontForFontGroup($db);
            $fontForFontGroup->fontName = $f->fontName;
            $fontForFontGroup->selectedFontId = $f->selectedFontId;
            $fontForFontGroup->specificSize = $f->specificSize;
            $fontForFontGroup->priceChange = $f->priceChange;
            $valResult = validateFontForFontGroup($fontForFontGroup, $db);
            if ($valResult != null) return validation_fail_response($valResult, 422);
            array_push($fontGroup->selectedFontList, $fontForFontGroup);
        }
        if (!$fontGroup->create()) {
            throw new Exception("failed to save fontGroup in db");
        }
        return array(
            "status" => "success",
            "error" => false,
            "message" => "font group created successfully",
            "data" => $fontGroup
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
    try {
        $db->beginTransaction();
        $fontGroup = new FontGroup($db);
        $fontGroup = $fontGroup->read($fontGroupId);
        if ($fontGroup == null) {
            return not_found_response("font group with id: " . $fontGroupId . " not found");
        }
        $fontGroup->fontGroupName = $data->fontGroupName;
        if (count($data->selectedFontList) < 2) {
            return validation_fail_response("atleast 2 fonts required", 422);
        } else if (duplicateFontsExist($data->selectedFontList)) {
            return validation_fail_response("fonts must be unique", 422);
        }
        $fontGroup->selectedFontList = array();
        foreach($data->selectedFontList as $f) {
            $fontForFontGroup = new FontForFontGroup($db);
            $fontForFontGroup->fontName = $f->fontName;
            $fontForFontGroup->fontGroupId = $fontGroupId;
            $fontForFontGroup->selectedFontId = $f->selectedFontId;
            $fontForFontGroup->specificSize = $f->specificSize;
            $fontForFontGroup->priceChange = $f->priceChange;
            $valResult = validateFontForFontGroup($fontForFontGroup, $db);
            if ($valResult != null) return validation_fail_response($valResult, 422);
            array_push($fontGroup->selectedFontList, $fontForFontGroup);
        }
        if (!$fontGroup->update()) {
            throw new Exception("failed to save fontGroup in db");
        }
        $db->commit();
        return array(
            "status" => "success",
            "error" => false,
            "message" => "font group updated successfully",
            "data" => $fontGroup
        );
    } catch (Exception $e) {
        $db->rollBack();
        return server_side_exception_response($e);        
    }
}

function deleteFontGroup($fontGroupId, $db) {
    try{
        $fontGroup = new FontGroup($db);
        $fontGroup = $fontGroup->read($fontGroupId);
        if ($fontGroup == null) {
            return not_found_response("font group with id: " . $fontGroupId . " not found");
        }
        $fontGroup->delete($fontGroupId);
        return array(
            "status" => "success",
            "error" => false,
            "message" => "font with id: " . $fontGroupId . " deleted"
        );
    } catch (Exception $e) {
        return server_side_exception_response($e);
    }
}

function duplicateFontsExist($fontForGroupList) {
    $size = count($fontForGroupList);
    for($i = 0; $i < $size; $i++) {
        for($j = $i+1; $j < $size; $j++) {
            if ($fontForGroupList[$i]->selectedFontId == $fontForGroupList[$j]->selectedFontId) {
                return true;
            }
        }
    }
    return false;
}

function validateFontForFontGroup($fontForFontGroup, $db) {
    $font = new Font($db);
    $font = $font->read($fontForFontGroup->selectedFontId);
    if ($font == null) {
        return "font with id: " . $fontForFontGroup->selectedFontId . " does not exist";
    } 
    // else if ($font->fontName != $fontForFontGroup->fontName) {
    //     return "font name: " . $fontForFontGroup->fontName 
    //     . " does not match font id: " . $fontForFontGroup->selectedFontId;
    // }
    return null;
}

?>