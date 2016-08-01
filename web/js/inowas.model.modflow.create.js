// initialize the map on the "map" div with a given center and zoom
//var area_map = L.map('area-map').setView([-31.36989525516968, -63.6753815432894], 11);

// Instantiate Map
var area_map = new L.Map('area-map', {zoom: 9, center: new L.latLng([41.575730,13.002411]) });

// Add layers
area_map.addLayer(L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {
    maxZoom: 18,
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
    '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
    'Imagery © <a href="http://mapbox.com">Mapbox</a>',
    id: 'mapbox.streets'
}));

// Add search control
area_map.addControl( new L.Control.Search({
    url: 'http://nominatim.openstreetmap.org/search?format=json&q={s}',
    jsonpParam: 'json_callback',
    propertyName: 'display_name',
    propertyLoc: ['lat','lon'],
    markerLocation: true,
    autoCollapse: true,
    autoType: false,
    minLength: 2,
    position: 'topright'
}) );

// Initialise the FeatureGroup to store editable layers
var drawnItems = new L.FeatureGroup();
area_map.addLayer(drawnItems);

var drawControlFull = new L.Control.Draw({
    draw : {
        position: 'topleft',
        polyline: false,
        marker: false
    }
});

var drawControlEditOnly = new L.Control.Draw({
    edit: {
        featureGroup: drawnItems
    },
    draw: false
});

area_map.addControl(drawControlFull);

area_map.on("draw:created", function (e) {
    var layer = e.layer;
    layer.addTo(drawnItems);
    drawControlFull.removeFrom(area_map);
    drawControlEditOnly.addTo(area_map)
});

area_map.on("draw:deleted", function(e) {
    drawControlEditOnly.removeFrom(area_map);
    drawControlFull.addTo(area_map);
});



