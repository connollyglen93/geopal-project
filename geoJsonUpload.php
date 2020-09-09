<?php
/**
 * Created by PhpStorm.
 * User: glen.connolly
 * Date: 07/09/2020
 * Time: 16:26
 */
include 'shutdown_manager.php';
include 'GeoJsonFileUploader.php';
$uploader = new GeoJsonFileUploader();
try{
    $uploader->handleUpload();
    echo "Upload OK";
}catch (UploadException $e){
    echo $e->getMessage();
}catch(\Throwable $t){
    $uploader->logError($t->getMessage());
    echo "An unexpected error occurred";
}