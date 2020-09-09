<?php
/**
 * Created by PhpStorm.
 * User: glen.connolly
 * Date: 07/09/2020
 * Time: 16:31
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

use classes\GeoJsonFileUploader;

include '../classes/GeoJsonFileUploader.php';
include '../functions/shutdown_manager.php';

// Retrieve GeoJSON stored in the session
$response = GeoJsonFileUploader::getStoredData();
// Attempt to decode the GeoJSON. This will validate the JSON format of the data and allow us to validate whether the stored data is empty or not
$geoJsonObj = json_decode($response, true);

if (!count($geoJsonObj) || !$geoJsonObj) { // Stored data is empty or Invalid GeoJson is stored
    http_response_code(404);
} else { // All good. Set a positive response code.
    http_response_code(200);
}
echo $response;