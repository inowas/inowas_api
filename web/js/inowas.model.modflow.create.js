// initialize the map on the "map" div with a given center and zoom
var area_map = L.map('area-map').setView(
    [-31.36989525516968, -63.6753815432894], 11
);


var streets = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {
    maxZoom: 18,
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
    '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
    'Imagery © <a href="http://mapbox.com">Mapbox</a>',
    id: 'mapbox.streets'
}).addTo(area_map);
var Hydda_Full = L.tileLayer('http://{s}.tile.openstreetmap.se/hydda/full/{z}/{x}/{y}.png', {
    attribution: 'Tiles courtesy of <a href="http://openstreetmap.se/" target="_blank">OpenStreetMap Sweden</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
});

// Initialise the FeatureGroup to store editable layers
var drawnItems = new L.FeatureGroup();
area_map.addLayer(drawnItems);

// Initialise the draw control and pass it the FeatureGroup of editable layers
var drawControl = new L.Control.Draw({
    draw : {
        position : 'topleft'
    },
    edit: {
        featureGroup: drawnItems
    }
});

area_map.addControl(drawControl);

area_map.on('draw:created', function (e) {
    var type = e.layerType,
        layer = e.layer;

    console.log(e);

    if (type === 'rectangle') {
        console.log(e);
        console.log(e.layer._latlngs[0].distanceTo(e.layer._latlngs[1]));
        console.log(e.layer._latlngs[0].distanceTo(e.layer._latlngs[3]));
        // Do marker specific actions
    }

    // Do whatever else you need to. (save to db, add to map etc)
    area_map.addLayer(layer);
});