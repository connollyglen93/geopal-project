<?php
/**
 * Created by PhpStorm.
 * User: glen.connolly
 * Date: 07/09/2020
 * Time: 15:28
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html>
<head>
    <title>GeoPal</title>
    <link rel="stylesheet" href="/leaflet/leaflet.css"/>
    <link rel="stylesheet" href="/css/index.css"/>

    <script src="/jquery/jquery-3.5.1.min.js"></script>
    <script src="/leaflet/leaflet.js"></script>
    <script src="/leaflet/editable/Leaflet.Editable.js"></script>
</head>
<body>
    <div id="mapid">
    </div>
    <div id="uploadForm" class="draggable" style="display: none;">
        <form enctype="multipart/form-data" id="geoJsonForm">
            <fieldset>
                <legend class="dragEl" id="uploadFormHeader">Upload GeoJson File</legend>
                <input name="geoJson" id="geoJsonFile" type="file" />
                <label for="geoJsonFile"><span>Select File To Upload</span></label>
                <input type="button" class="uploadBtn" onclick="uploadGeoJson()" value="Upload" />
                <button type="button" class="uploadBtn" onclick="toggleUploadForm()">Cancel</button>
            </fieldset>
        </form>
    </div>

    <div id="colorSelection" class="draggable" style="display: none;">
            <fieldset>
                <legend class="dragEl" id="colorSelectionHeader">Change Icon Color</legend>
                <label for="colorSelect"><span>Pick New Color for Map Icons</span></label>
                <select id="colorSelect" name="colorSelect">
                    <option value="#0000FF">Blue</option>
                    <option value="#FF0000">Red</option>
                    <option value="#FFA500">Orange</option>
                    <option value="#00FF00">Green</option>
                    <option value="#800080">Purple</option>
                    <option value="#FFC0CB">Pink</option>
                </select>
                <button type="button" class="uploadBtn" onclick="changeColor(document.getElementById('colorSelect'))">Change</button>
                <button type="button" class="uploadBtn" onclick="toggleColorSelection()">Cancel</button>
            </fieldset>
    </div>
</body>
    <script src="/js/index.js"></script>
</html>