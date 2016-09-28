// initialize the map on the "map" div with a given center and zoom
//var area_map = L.map('area-map').setView([-31.36989525516968, -63.6753815432894], 11);

var model = {};
model.area = {};
model.grid_size = {};
model.soil_model = {};

// Instantiate Map
var area_map = new L.Map('area-map', {
    zoom: 9,
    center: new L.latLng([41.575730,13.002411])
});

area_map.addControl( new L.Control.FullScreen({
    position: 'bottomright',
    forceSeparateButton: true
}));

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
        circle: false,
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
    drawControlEditOnly.addTo(area_map);

    model.area.geoJSON = JSON.stringify(layer.toGeoJSON());

    var html = "<h2>Area properties</h2>";
    html += "<h3>Points</h3>";
    html += createHtmlTable(layer._latlngs);
    html += "<h3>Surface</h3>";
    html += "<p>" + L.GeometryUtil.readableArea(L.GeometryUtil.geodesicArea(layer.getLatLngs()), true) + "</p>";
    html += "<h3>Length</h3>";
    html += "<p>" + L.GeometryUtil.readableDistance(calculateDistance(layer), true, false) + "</p>";
    $("#area-map-description").html(html);
});

area_map.on("draw:deleted", function(e) {
    drawControlEditOnly.removeFrom(area_map);
    drawControlFull.addTo(area_map);

    $("#area-map-description").html('<h3>Please draw your model-area</h3>');
});

function createHtmlTable(points){
    var html = '<table class="table">';
    html += '<tr><th>#</th><th>Lat</th><th>Long</th></tr>';
    for (var i=0; i<points.length; i++){
         html += '<tr><td>' + i + '</td>' + '<td>' + round(points[i].lat, 5) + '</td>' + '<td>' + round(points[i].lng, 5) + '</td></tr>';
    }
    html += '</table>';
    return html;
}

function round(value, decimals) {
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}

function calculateDistance(layer){
    var tempLatLng = null;
    var totalDistance = 0.00000;
    $.each(layer._latlngs, function(i, latlng){
        if(tempLatLng == null){
            tempLatLng = latlng;
            return;
        }

        totalDistance += tempLatLng.distanceTo(latlng);
        tempLatLng = latlng;
    });

    return totalDistance;
}

$('#btn_create').click(function(){
    model.name = $( '#modelname' ).val();
    model.description = $( '#description' ).val();
    model.soil_model.numberOfLayers = $( '#soilmodel_number_of_layers' ).val();

    var gridSizeArray = $('#gridsize').val().split("x");
    model.grid_size.cols = gridSizeArray[0];
    model.grid_size.rows = gridSizeArray[1];

    $.post("/api/modflowmodels.json",
        function(data, status){
            if (status == "success"){
                window.location.href = "/models/modflow/"+data.id;
            } else {
                alert("There was a Problem submitting the data.");
            }
        }
    );
});
