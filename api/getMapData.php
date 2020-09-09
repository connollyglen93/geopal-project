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

$response = GeoJsonFileUploader::getStoredData();

$geoJsonObj = json_decode($response, true);

if(!count($geoJsonObj)){
    http_response_code(404);
}else{
    http_response_code(200);
}
echo $response;