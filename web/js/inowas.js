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

    var popup = L.popup();

    function onMapClick(e) {
        popup
            .setLatLng(e.latlng)
            .setContent("You clicked the map at " + e.latlng.toString())
            .openOn(boundary_map);
    }

    boundary_map.on('click', onMapClick);

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
        if ("cw" in wellData) {
            wellData.cw.forEach(function (item) {
                L.circle([item.point.y, item.point.x], 10, {color: 'red'}).bindPopup("Well "+item.name).addTo(wells);
            });
        }

        if ("iw" in wellData) {
            wellData.iw.forEach(function (item) {
                L.circle([item.point.y, item.point.x], 10, {color: 'grey'}).bindPopup("Well "+item.name).addTo(wells);
            });
        }

        if ("snw" in wellData) {
            wellData.snw.forEach(function (item) {
                L.circle([item.point.y, item.point.x], 10, {color: 'green'}).bindPopup("Well " + item.name).addTo(wells);
            });
        }
        wells.addTo(boundary_map);
    });

    var chb = new L.LayerGroup();
    $.get( "/api/modflowmodels/"+modelId+"/constant_head.json?geojson=true&srid=4326", function ( data ) {
        L.geoJson(data, {color: 'red'}).addTo(chb);
        chb.addTo(boundary_map);
    });

    var riv = new L.LayerGroup();
    $.get( "/api/modflowmodels/"+modelId+"/rivers.json?geojson=true&srid=4326", function ( data ) {
        L.geoJson(data, {color: 'red'}).addTo(riv);
        riv.addTo(boundary_map);
    });

    var baseMaps = {"Streets": streets, "Hydda_Full": Hydda_Full};
    var overlayMaps = {"Area": area, "Wells": wells, "CHB": chb, "RIV": riv};
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

    var results_map = L.map('results-map').setView(
        [21.033333, 105.85], 12
    );

    var streets = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {
        maxZoom: 18,
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
        '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
        'Imagery © <a href="http://mapbox.com">Mapbox</a>',
        id: 'mapbox.streets'
    });
    streets.addTo(results_map);

    var area = new L.LayerGroup();
    $.getJSON( "/api/modflowmodels/"+modelId+"/contents/soilmodel.json", function ( data ) {
        var polygon = L.geoJson(jQuery.parseJSON(data.geojson), {"weight": 2, "fillOpacity": 0}).bindPopup("Groundwater model area Hanoi II.");
        polygon.addTo(area);
        area.addTo(results_map);
        results_map.fitBounds(polygon.getBounds());
    });

    $.getJSON( "/api/modflowmodels/"+modelId+"/boundingbox.json?srid=4326", function ( boundingBox ) {
        var imageUrl = "/api/modflowmodels/"+modelId+"/layers/3/properties/hh.json?_format=png";
        imgOverlay = L.imageOverlay(imageUrl, boundingBox).addTo(results_map).setOpacity(0.6);
    });

    loadLayerImg(modelId, 3, 'hh', true);

    var grid = new L.LayerGroup();
    $.get( "/api/modflowmodels/"+modelId+"/grid.json?srid=4326", function (data) {
        L.geoJson(data, {"color": "blue", "weight": 1, "opacity": 0.65, "fillOpacity": 0.1}).addTo(grid);
        grid.addTo(results_map);
    });

    var baseMaps = {};
    var overlayMaps = {"Area":area, "Grid": grid};
    L.control.layers(baseMaps, overlayMaps).addTo(results_map);

    results_map.on('baselayerchange', function(e) {
        console.log(e);
    });

    var chart_heads = c3.generate({
        bindto: '#result_chart_heads',
        size: {
            height: 220
        },
        padding: {
            top: 0,
            right: 25,
            left: 35
        },
        legend: {
            position: 'inset'
        },
        data: {
            x: 'x',
//        xFormat: '%Y%m%d', // 'xFormat' can be used as custom format of 'x'
            columns: [
                ['x', '2013-01-01', '2013-01-02', '2013-01-03', '2013-01-04', '2013-01-05', '2013-01-06'],
//            ['x', '20130101', '20130102', '20130103', '20130104', '20130105', '20130106'],
                ['Layer 1', 30, 200, 100, 400, 150, 250],
                ['Layer 2', 130, 340, 200, 500, 250, 350]
            ]
        },
        axis: {
            x: {
                type: 'timeseries',
                tick: {
                    format: '%Y-%m-%d'
                }
            }
        }
    });

    setTimeout(function () {
        chart_heads.load({
            columns: [
                ['Layer 3', 400, 500, 450, 700, 600, 500]
            ]
        });
    }, 1000);

    var chart_boundaries = c3.generate({
        bindto: '#result_chart_boundaries',
        size: {
            height: 200
        },
        padding: {
            right: 25,
            left: 35
        },
        legend: {
            position: 'inset'
        },
        data: {
            x: 'x',
//        xFormat: '%Y%m%d', // 'xFormat' can be used as custom format of 'x'
            columns: [
                ['x', '2013-01-01', '2013-01-02', '2013-01-03', '2013-01-04', '2013-01-05', '2013-01-06'],
//            ['x', '20130101', '20130102', '20130103', '20130104', '20130105', '20130106'],
                ['River Stage', 30, 200, 100, 400, 150, 250],
            ]
        },
        axis: {
            x: {
                type: 'timeseries',
                tick: {
                    format: '%Y-%m-%d'
                }
            }
        }
    });
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



