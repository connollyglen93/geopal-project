<?php
/**
 * Created by PhpStorm.
 * User: glen.connolly
 * Date: 07/09/2020
 * Time: 15:58
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html; charset=UTF-8");
?>
<div id="map-interaction-buttons">
    <button type="button" onclick="toggleUploadForm()">Upload GeoJson File</button>
    <button type="button" onclick="enterRectangleMode()">Lasso</button>
    <button type="button" onclick="toggleColorSelection()">Change Icon Color</button>
</div>