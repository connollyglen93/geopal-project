<?php
/**
 * Created by PhpStorm.
 * User: glen.connolly
 * Date: 07/09/2020
 * Time: 16:26
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use classes\GeoJsonFileUploader;
use classes\UploadException;

include '../functions/shutdown_manager.php';
include '../classes/GeoJsonFileUploader.php';

// Set filepath to be relative so the application can access 'tmp' folder
chdir('../');
if (!is_dir('tmp')) {
    mkdir('tmp');
}

$uploader = new GeoJsonFileUploader();
try {
    $uploader->handleUpload();
    http_response_code(201);
    echo json_encode(["status" => true, 'message' => 'File Uploaded!']);
} catch (UploadException $e) {
    // UploadException is a custom exception. When this exception is thrown, the returned message is user friendly and can be output
    http_response_code(400);
    echo json_encode(["status" => false, 'message' => htmlspecialchars($e->getMessage())]);
} catch (\Throwable $t) {
    // If an unexpected error is thrown, we will log the error and tell the user there was an unexpected error
    $uploader->logError($t->getTraceAsString());
    http_response_code(503);
    echo json_encode(["status" => false, 'message' => "An unexpected error occurred"]);
}