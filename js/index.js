var map = L.map('mapid',{editable: true}).setView([53.392603, -7.893307], 7);
var masterLayer = {};
var rectangle = {};
L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiY29ubm9sbHlnbGVuOTMiLCJhIjoiY2p6ODZxbng0MGl3NTNmbnl6b3dleWVhbCJ9.0mJQQZufz3WkWIDaU7gi2g', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox.streets',
}).addTo(map);

const uploadFormEl = document.getElementById('uploadForm'),
    colorSelect = document.getElementById('colorSelection');

const getMarkerHtml = (color) => {
    return `
          background-color: ${color};
          width: 0.75rem;
          height: 0.75rem;
          display: block;
          left: -0.5rem;
          top: -0.5rem;
          position: relative;
          border-radius: 3rem;
          border: 1px solid #FFFFFF
    `;
};

function getIcon(color = 'blue'){
    return L.divIcon({
        className: "marker",
        html: `<span style="${getMarkerHtml(color)}" />`,
        popupAnchor: [-7, -20]
    });
}

$('#geoJsonFile').on('change', () => {
    let fileEl = $('#geoJsonFile');
    let file = fileEl[0].files[0];

    if (file.size > 1024 * 1024) {
        alert('Max upload size is 1MB');
        fileEl.val('');
    }

    if (file.type !== 'application/json') {
        alert('File must be of type JSON');
        fileEl.val('');
    }
});

map.on('editable:vertex:dragend', (e) => {
    let rectBounds = e.layer.getBounds();
    let lassoedLayers = [];
    masterLayer.eachLayer(function(layer) {
        if( layer instanceof L.Marker && rectBounds.contains(layer.getLatLng())){
            lassoedLayers.push(layer);
        }
    });
    removeMarkersTable();
    if(lassoedLayers.length) {
        buildMarkersTable(lassoedLayers);
    }
});

const removeRectangle = () => {
  if( typeof rectangle.remove === "function" ){
        rectangle.remove();
        rectangle = {};
  }
};

const enterRectangleMode = () => {
    removeRectangle();
    rectangle = map.editTools.startRectangle();
};

const removeMarkersTable = () => {
    if(map.markersTable){
        map.removeControl(map.markersTable);
        map.markersTable = false;
    }
};

const buildMarkersTable = async (layers) => {
    let botLeftDiv = L.control({position: "bottomleft"});

    botLeftDiv.onAdd = function (m) {
        map.markersTable = this;
        var div = L.DomUtil.create("div", "marker-table");
        div.innerHTML = `
                    <fieldset>
                    <legend>Selected Marker Points</legend>
                    <table>
                        ${getMarkerTableHead()}
                        ${getMarkerTableBody(layers)}
                    </table>
                    </fieldset>`;
        return div;
    };
    botLeftDiv.addTo(map);
};

const zoomToGeometry = (rowEl) => {
    let coords = JSON.parse(rowEl.getAttribute('feature'));
    // masterLayer.getLayer(coords[2]).openPopup([coords[1], coords[0]]);
    map.flyTo([coords[1], coords[0]], 16);
};

const reduceLayerToRow = (acc, layer) => {
    let address = layer.feature.properties.hasOwnProperty('address') ? layer.feature.properties.address : 'Unknown';
    layer.feature.geometry.coordinates.push(layer._leaflet_id);
    let jsonCoords = JSON.stringify(layer.feature.geometry.coordinates);
    return acc + `
        <tr onclick="zoomToGeometry(this)" feature="${jsonCoords}">
            <td>${address}</td>
            <td>${layer.feature.geometry.coordinates[1]}</td>
            <td>${layer.feature.geometry.coordinates[0]}</td>
        </tr>
    `
};

const getMarkerTableBody = (layers) => {
    let layerRows = layers.reduce(reduceLayerToRow, '');
    return `
        <tbody>
            ${layerRows}
        </tbody>
    `;
};

const getMarkerTableHead = () => {
    return `
        <thead>
            <tr>
                <th style="width:40%">Address</th>
                <th style="width:30%">Lat</th>
                <th style="width:30%">Long</th>
            </tr>
        </thead>  
    `;
};

const retrieveGeoJson = async () => {
    try {
        let response = await fetch(`/api/getMapData.php`);
        return await response.json();
    }catch(err){
        console.error(err);
    }
};

function removeGeoJsonLayer(){
    if(typeof masterLayer.eachLayer !== 'function'){
        return;
    }
    masterLayer.eachLayer(function(layer) {
        if(map.hasLayer(layer)){
            map.removeLayer(layer);
        }
    });
    masterLayer = {};
}

const reducePropertyToRow = (acc, property) => {
    let propertyName = property[0].toUpperCase()[0] + property[0].substring(1);
    return `${acc}<tr><td align="left"><b>${propertyName}:</b></td><td align="left">${property[1]}</td></tr>`
};

const generatePointDetails = (point) => {
    return `
            <div>
                <h2>Details</h2>
                <div>
                    <table border="0">
                        ${Object.entries(point.properties).reduce(reducePropertyToRow, '')}
                    </table>
                </div>
            </div>`;
};

function drawGeoJsonLayer(geoJsonData) {
    removeGeoJsonLayer();
    if(typeof geoJsonData === 'object') {
        let geoJsonLayer = L.geoJSON(geoJsonData, {
            pointToLayer: function (feature, latlng) {
                let marker = L.marker(latlng, {icon: getIcon()});
                marker.bindPopup(generatePointDetails(feature), {
                    maxWidth: "auto"
                });
                return marker;
            }
        });
        geoJsonLayer.addTo(map);
        masterLayer = geoJsonLayer;
        map.fitBounds(geoJsonLayer.getBounds());
    }
}

(async () => {
    drawGeoJsonLayer(await retrieveGeoJson());
})();

function uploadGeoJson() {
    $.ajax({
        // Your server script to process the upload
        url: '/api/geoJsonUpload.php',
        type: 'POST',

        // Form data
        data: new FormData($('#geoJsonForm')[0]),

        // Tell jQuery not to process data or worry about content-type
        // You *must* include these options!
        cache: false,
        contentType: false,
        processData: false,

        // Custom XMLHttpRequest
        success: async () => {
            drawGeoJsonLayer(await retrieveGeoJson());
        },
        error: function (data) {
            console.error(data);
        },
        complete: function () {
            toggleUploadForm();
            removeRectangle();
        }
    });
}

function changeColor(selection){
    let color = selection.value;
    masterLayer.eachLayer(function(layer) {
        if(typeof layer.setIcon === 'function') {
            layer.setIcon(getIcon(color));
        }else{
            //Compatibility for FeatureCollection, Polygon or MultiPolygon GeoJSON
            layer.setStyle({color: color});
        }
    });
}

function toggleColorSelection(){
    if (colorSelect.style.display !== 'none') {
        colorSelect.style.display = 'none';
    } else {
        colorSelect.style.display = 'block';
    }
}

function toggleUploadForm() {
    if (uploadFormEl.style.display !== 'none') {
        uploadFormEl.style.display = 'none';
    } else {
        uploadFormEl.style.display = 'block';
    }
}

(async () => {
    let topRightDiv = L.control({position: "topright"});

    fetch('/api/mapInteraction.php')
        .then((response) => {
            return response.text();
        })
        .then((mapInteractionView) => {
            topRightDiv.onAdd = function (map) {
                var div = L.DomUtil.create("div", "map-interaction");
                div.innerHTML = `${mapInteractionView}`;
                return div;
            };
            topRightDiv.addTo(map);
        })
})();

let fileInput = document.querySelector('#geoJsonFile');

fileInput.addEventListener('change', (e) => {
    let label	 = fileInput.nextElementSibling,
        labelVal = label.innerHTML,
        fileName = e.target.value.split('\\' ).pop();

    if (fileName)
        label.querySelector('span').innerHTML = fileName;
    else
        label.innerHTML = labelVal;
});

const draggableElements = document.getElementsByClassName('draggable');
for (var i = 0; i < draggableElements.length; i++) {
    dragElement(draggableElements.item(i));
}

function dragElement(elmnt) {
    let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    if (document.getElementById(elmnt.id + "Header")) {
        // if present, the Header is where you move the DIV from:
        document.getElementById(elmnt.id + "Header").onmousedown = dragMouseDown;
    } else {
        // otherwise, move the DIV from anywhere inside the DIV:
        elmnt.onmousedown = dragMouseDown;
    }


    function dragMouseDown(e) {
        e = e || window.event;
        e.preventDefault();
        // get the mouse cursor position
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        // call the drag function whenever the cursor moves:
        document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        // calculate the new cursor position
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        // set the element's new position
        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
    }

    function closeDragElement() {
        // stop moving when mouse button is released:
        document.onmouseup = null;
        document.onmousemove = null;
    }
};