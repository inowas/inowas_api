hide_all = function () {
    $( "#summary" ).hide();
    $( "#soilmodel" ).hide();
    $( "#boundaries" ).hide();
    $( "#calculation" ).hide();
    $( "#results" ).hide();
    $( "#history" ).hide();
    $( ".summary" ).removeClass('active');
    $( ".soilmodel" ).removeClass('active');
    $( ".boundaries" ).removeClass('active');
    $( ".calculation" ).removeClass('active');
    $( ".results" ).removeClass('active');
    $( ".history" ).removeClass('active');
};

$( ".summary" ).click(function(){
    hide_all();
    $( "#summary" ).show();
    $( ".summary" ).addClass('active');

    // initialize the map on the "map" div with a given center and zoom
    var area_map = L.map('area-map').setView(
        [21.033333, 105.85], 9
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

    var area = new L.LayerGroup();
    $.getJSON( "/api/modflowmodels/"+modelId+"/contents/summary.json", function ( data ) {
        $(".content_summary").html( data.html );
        var polygon = L.geoJson(jQuery.parseJSON(data.geojson)).bindPopup("Groundwater model area Hanoi II.");
        polygon.addTo(area);
        area.addTo(area_map);
        area_map.fitBounds(polygon.getBounds());
    });
});

$( ".soilmodel" ).click(function(){
    hide_all();
    $( "#soilmodel" ).show();
    $( ".soilmodel" ).addClass('active');

    soilmodel_map = L.map('soilmodel-map').setView([21.033333, 105.85], 12);

    var streets = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {
        maxZoom: 18,
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
        '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
        'Imagery © <a href="http://mapbox.com">Mapbox</a>',
        id: 'mapbox.streets'
    });
    streets.addTo(soilmodel_map);

    var area = new L.LayerGroup();
    $.getJSON( "/api/modflowmodels/"+modelId+"/contents/soilmodel.json", function ( data ) {
        $(".content_soilmodel").html( data.html );
        var polygon = L.geoJson(jQuery.parseJSON(data.geojson)).addTo(soilmodel_map).bindPopup("Groundwater model area Hanoi II.");
        polygon.addTo(area);
        area.addTo(soilmodel_map);
        soilmodel_map.fitBounds(polygon.getBounds());
    });

    loadLayerImg(modelId, 0, 'et', true);
});

$( ".boundaries" ).click(function () {
    hide_all();
    $( "#boundaries" ).show();
    $( ".boundaries" ).addClass('active');

    var boundary_map = L.map('boundaries-map').setView(
        [21.033333, 105.85], 12
    );

    var streets = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {
        maxZoom: 18,
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
        '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
        'Imagery © <a href="http://mapbox.com">Mapbox</a>',
        id: 'mapbox.streets'
    }).addTo(boundary_map);
    var Hydda_Full = L.tileLayer('http://{s}.tile.openstreetmap.se/hydda/full/{z}/{x}/{y}.png', {
        attribution: 'Tiles courtesy of <a href="http://openstreetmap.se/" target="_blank">OpenStreetMap Sweden</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    });

    var area = new L.LayerGroup();
    $.getJSON( "/api/modflowmodels/"+modelId+"/contents/soilmodel.json", function ( data ) {
        var polygon = L.geoJson(jQuery.parseJSON(data.geojson)).bindPopup("Groundwater model area Hanoi II.");
        polygon.addTo(area);
        area.addTo(boundary_map);
        boundary_map.fitBounds(polygon.getBounds());
    });

    var wells = new L.LayerGroup();
    $.getJSON( "/api/modflowmodels/"+modelId+"/wells.json?srid=4326", function ( wellData ) {
        wellData.forEach(function (item) {
            L.circle([item.point.y, item.point.x], 10, {color: 'red'}).bindPopup("Well "+item.name).addTo(wells);
        });
        wells.addTo(boundary_map);
    });

    var chb = new L.LayerGroup();
    $.get( "/api/modflowmodels/"+modelId+"/constant_head.json?geojson=true&srid=4326", function ( data ) {
        L.geoJson(data, {color: 'red'}).addTo(chb);
        chb.addTo(boundary_map);
    });

    var baseMaps = {"Streets": streets, "Hydda_Full": Hydda_Full};
    var overlayMaps = {"Area": area, "Wells": wells, "CHB": chb};
    L.control.layers(baseMaps, overlayMaps).addTo(boundary_map);
});

$( ".calculation" ).click(function(){
    hide_all();
    $( "#calculation" ).show();
    $( ".calculation" ).addClass('active');
});

$( ".results" ).click(function(){
    hide_all();
    $( "#results" ).show();
    $( ".results" ).addClass('active');
});

$( ".history" ).click(function(){
    hide_all();
    $( "#history" ).show();
    $( ".history" ).addClass('active');
});

var imgOverlay;
loadLayerImg = function(modelId, layerOrder, propertyTypeAbbreviation, ft){
    if (ft!=true) {soilmodel_map.removeLayer(imgOverlay);}
    $.getJSON( "/api/modflowmodels/"+modelId+"/boundingbox.json?srid=4326", function ( boundingBox ) {
        var imageUrl = "/api/modflowmodels/"+modelId+"/layers/"+layerOrder+"/properties/"+propertyTypeAbbreviation+".json?_format=png";
        imgOverlay = L.imageOverlay(imageUrl, boundingBox).addTo(soilmodel_map).setOpacity(0.6);
    });
};



