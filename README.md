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