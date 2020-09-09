<?php
/**
 * Created by PhpStorm.
 * User: glen.connolly
 * Date: 07/09/2020
 * Time: 16:31
 */
include 'shutdown_manager.php';
include 'GeoJsonFileUploader.php';
echo GeoJsonFileUploader::getStoredData();