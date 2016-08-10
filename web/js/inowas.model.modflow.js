hide_all = function () {
    $( "#summary" ).hide();
    $( "#area" ).hide();
    $( "#soilmodel" ).hide();
    $( "#boundaries" ).hide();
    $( "#wells" ).hide();
    $( "#calculation" ).hide();
    $( "#results" ).hide();
    $( "#history" ).hide();
    $( "#delete" ).hide();
    $( ".summary" ).removeClass('active');
    $( ".area" ).removeClass('active');
    $( ".soilmodel" ).removeClass('active');
    $( ".boundaries" ).removeClass('active');
    $( ".wells" ).removeClass('active');
    $( ".calculation" ).removeClass('active');
    $( ".results" ).removeClass('active');
    $( ".history" ).removeClass('active');
    $( ".delete" ).removeClass('active');
};

$( ".summary" ).click(function(){
    hide_all();
    $( "#summary" ).show();
    $( ".summary" ).addClass('active');
    I.model.loadSummary();
});

$( ".area" ).click(function(){
    hide_all();
    $( "#area" ).show();
    $( ".area" ).addClass('active');
    I.model.loadArea();
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
    $.getJSON( "/api/modflowmodels/"+I.model.id+"/contents/soilmodel.json", function ( data ) {
        $(".content_soilmodel").html( data.html );
        var polygon = L.geoJson(jQuery.parseJSON(data.geojson)).addTo(soilmodel_map).bindPopup("Groundwater model area Hanoi II.");
        polygon.addTo(area);
        area.addTo(soilmodel_map);
        soilmodel_map.fitBounds(polygon.getBounds());
    });

    loadLayerImg(I.model.id, 0, 'et', true);
});

$( ".boundaries" ).click(function () {
    hide_all();
    $( "#boundaries" ).show();
    $( ".boundaries" ).addClass('active');

    I.model.loadBoundaries();

});

$( ".wells" ).click(function() {
    hide_all();
    $( "#wells" ).show();
    $( ".boundaries" ).addClass('active');
    $( ".wells" ).addClass('active');

    I.model.loadWells();
});

$( ".calculation" ).click(function(){
    hide_all();
    $( "#calculation" ).show();
    $( ".calculation" ).addClass('active');

    $.getJSON( "/api/modflowmodels/"+I.model.id+"/contents/calculation.json", function ( data ) {
        $(".content_calculation").html( data.html );
    });
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
    $.getJSON( "/api/modflowmodels/"+I.model.id+"/contents/soilmodel.json", function ( data ) {
        var polygon = L.geoJson(jQuery.parseJSON(data.geojson), {"weight": 2, "fillOpacity": 0}).bindPopup("Groundwater model area Hanoi II.");
        polygon.addTo(area);
        area.addTo(results_map);
        results_map.fitBounds(polygon.getBounds());
    });

    var wells = new L.LayerGroup();
    $.getJSON( "/api/modflowmodels/"+I.model.id+"/wells.json?srid=4326", function ( wellData ) {

        if ("ow" in wellData) {
            wellData.ow.forEach(function (item) {
                L.circle([item.point.y, item.point.x], 2, {color: 'black', weight: 1, fillColor: 'blue', fillOpacity: 0.7}).bindPopup("Well "+item.name).addTo(wells);
            });
        }

        wells.addTo(results_map);
    });

    $.getJSON( "/api/modflowmodels/"+I.model.id+"/boundingbox.json?srid=4326", function ( boundingBox ) {
        var imageUrl = "/api/modflowmodels/"+model.id+"/layers/3/properties/hh.json?_format=png";
        imgOverlay = L.imageOverlay(imageUrl, boundingBox).addTo(results_map).setOpacity(0.6);
    });

    loadLayerImg(model.id, 3, 'hh', true);

    var grid = new L.LayerGroup();
    $.get( "/api/modflowmodels/"+I.model.id+"/grid.json?srid=4326", function (data) {
        L.geoJson(data, {"color": "blue", "weight": 1, "opacity": 0.65, "fillOpacity": 0.1}).addTo(grid);
    });

    var baseMaps = {};
    var overlayMaps = {"Area":area, "Grid": grid, "ObservationWells": wells};
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
            columns: [
                ['OP1', -20.435199737548828, -21.736900329589844, -22.839399337768555, -23.545799255371094, -23.988100051879883, -24.32270050048828, -24.57859992980957, -24.775299072265625, -24.90410041809082, -24.997800827026367, -25.1072998046875, -25.236799240112305, -25.38920021057129, -25.542999267578125, -25.66659927368164, -25.80500030517578, -25.948400497436523, -26.083900451660156, -26.177600860595703, -26.23740005493164, -26.264400482177734, -26.292800903320312, -26.363300323486328, -26.462799072265625, -26.564199447631836, -26.545700073242188, -26.58799934387207, -26.663999557495117, -26.751800537109375, -26.836999893188477, -26.925100326538086, -27.002599716186523, -27.021299362182617, -27.045700073242188, -27.09709930419922],
                ['OP2', -13.170000076293945, -14.388500213623047, -14.872200012207031, -15.23169994354248, -15.479000091552734, -15.698399543762207, -15.84179973602295, -15.813799858093262, -15.684700012207031, -15.657899856567383, -15.754400253295898, -15.880800247192383, -16.06100082397461, -16.228200912475586, -16.345300674438477, -16.45599937438965, -16.576000213623047, -16.652999877929688, -16.660499572753906, -16.558000564575195, -16.39889907836914, -16.359100341796875, -16.449100494384766, -16.575000762939453, -16.696199417114258, -16.837099075317383, -16.95330047607422, -17.06209945678711, -17.12070083618164, -17.175199508666992, -17.27280044555664, -17.159799575805664, -17.024499893188477, -16.98390007019043, -17.001800537109375],
                ['OP3', -3.296999931335449, -3.674639940261841, -3.818269968032837, -3.899440050125122, -4.010129928588867, -4.05679988861084, -3.76951003074646, -3.005189895629883, -2.457350015640259, -2.6702499389648438, -3.0540599822998047, -3.3350300788879395, -3.7331600189208984, -4.0751800537109375, -4.321579933166504, -4.4423298835754395, -4.4706501960754395, -4.291409969329834, -4.046480178833008, -3.4232800006866455, -2.8559999465942383, -3.05036997795105, -3.343369960784912, -3.591939926147461, -3.9480700492858887, -4.194540023803711, -4.38224983215332, -4.488329887390137, -4.411409854888916, -4.499760150909424, -4.511549949645996, -3.6645700931549072, -3.3840999603271484, -3.3560500144958496, -3.414109945297241]
            ]
        }
    });

    setTimeout(function () {
        chart_heads.load({
            columns: [

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
            columns: [
                ['River Stage', 2.54, 4.27, 3.45, 2.61, 2.43, 3.57, 4.58, 5.98, 5.82, 2.79, 2.51, 2.24, 2.06, 1.91, 2.18, 2.9, 5.02, 2.56, 5.17, 3.66, 3.72, 2.52, 1.85, 1.61, 1.59, 1.52, 1.22, 1.85, 2.32, 2.57, 4.25, 4.54, 4.13, 3.39, 2.05, 1.48]
            ]
        },
    });
});

$( ".history" ).click(function(){
    hide_all();
    $( "#history" ).show();
    $( ".history" ).addClass('active');
});

$( ".delete" ).click(function(){
    hide_all();
    $( "#delete" ).show();
    $( ".delete" ).addClass('active');
});

$(document).on('click', '.btn_calculation', function(event){
        console.log(event);
        $.post( "/api/modflowmodels/"+I.model.id+"/calculations.json", function ( data ) {
            console.log(data);
            $(".content_calculation").html( data.html );
        }, 'json');
    }
);

var imgOverlay;
loadLayerImg = function(modelId, layerOrder, propertyTypeAbbreviation, ft){
    if (ft!=true) {soilmodel_map.removeLayer(imgOverlay);}
    $.getJSON( "/api/modflowmodels/"+modelId+"/boundingbox.json?srid=4326", function ( boundingBox ) {
        var imageUrl = "/api/modflowmodels/"+modelId+"/layers/"+layerOrder+"/properties/"+propertyTypeAbbreviation+".json?_format=png";
        imgOverlay = L.imageOverlay(imageUrl, boundingBox).addTo(soilmodel_map).setOpacity(0.6);
    });
};

$( "#btn_delete_model").click(function () {
    $.ajax({
        type: 'DELETE',
        url: '/api/modflowmodels/'+I.model.id+'.json',
        statusCode: {
            200: function() {
                window.location.href = "/models/modflow";
            }
        }
    });
});

$( "#btn_save_area").click(function () {
    if (I.model.updateProperties( I.model.id ) == true) {
        $( "#btn_save_area" ).hide();
    }
});