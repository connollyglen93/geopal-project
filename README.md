# GeoPal

## Spec

Create a webpage using whatever technologies you feel appropriate to allow a user to:
1. Upload a GeoJson File to display points on a map
2. Allow user to lasso points to display in a grid
3. Allow user to change the color of all the icons displayed

## Introduction

This is a simple PHP application using Javascript and Leaflet.js to display and analyze an uploaded GeoJson dataset. 
In the interest of minimalism, no frontend or backend framework was used to build this webpage. 
This webpage was built using PHP 7.0.33, HTML, CSS and Javascript

## Installation

Clone the repository

```bash
git clone https://github.com/connollyglen93/geopal-project.git
```

Run the application, with the built-in PHP web server

```bash
php -S localhost:8000
```

Check if it works in the browser

```bash
go to http://localhost:8000/
```

Sample data for testing the application can be found at
```bash
  sample/
```

## API Contract

### Route:
`/api/getMapData.php`

#### Method:
`GET`

#### Required Headers:
```bash
Content-Type: application/json
```

#### Request
Params: _None_

#### Response

Possible HTTP Status Codes:
    `200`, `404`

Headers:
```bash
Access-Control-Allow-Origin: *
Content-Type: application/json; charset=UTF-8
```

Body:
A GeoJson string. e.g.
```javascript
    {
        "type": "FeatureCollection",
        "features": [
            {
                "type": "Feature",
                "geometry": {
                    "type": "Point",
                    "coordinates": [
                        102,
                        0.5
                    ]
                },
                "properties": {
                    "prop0": "value0"
                }
            }
        ]
    }
```
### Route:
`/api/geoJsonUpload.php`

#### Method:
`POST`

#### Required Headers:
```bash
Content-Type: application/json
```

#### Request
Params: `geoJson: A .json or .geojson file with a mime type of application/json or text/plain
          which is no larger than 1MB`

#### Response

Possible HTTP Status Codes:
    `201`, `401`, `503`

Headers:
```bash
Access-Control-Allow-Origin: *
Content-Type: application/json; charset=UTF-8
```

Body:
A json string. e.g.
```javascript
    {"status":true,"message":"File Uploaded!"}
```
### Route:
`/api/mapInteraction.php`

#### Method:
`GET`

#### Required Headers:
```bash
Content-Type: application/json
```

#### Request
Params: _None_

#### Response

Possible HTTP Status Codes:
    `200`

Headers:
```bash
Access-Control-Allow-Origin: *
Content-Type: application/json; charset=UTF-8
```

Body:
HTML. e.g.
```html
    <div id="map-interaction-buttons">
        <button type="button" onclick="toggleUploadForm()">Upload GeoJson File</button>
        <button type="button" onclick="enterRectangleMode()">Lasso</button>
        <button type="button" onclick="toggleColorSelection()">Change Icon Color</button>
    </div>    
```
