hide_all = function () {
    $( "#summary" ).hide();
    $( "#area" ).hide();
    $( "#soilmodel" ).hide();
    $( "#boundaries" ).hide();
    $( "#wells" ).hide();
    $( "#rivers" ).hide();
    $( "#calculation" ).hide();
    $( "#results" ).hide();
    $( "#history" ).hide();
    $( "#delete" ).hide();
    $( ".summary" ).removeClass('active');
    $( ".area" ).removeClass('active');
    $( ".soilmodel" ).removeClass('active');
    $( ".boundaries" ).removeClass('active');
    $( ".wells" ).removeClass('active');
    $( ".rivers" ).removeClass('active');
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

$( ".rivers" ).click(function() {
    hide_all();
    $( "#rivers" ).show();
    $( ".boundaries" ).addClass('active');
    $( ".rivers" ).addClass('active');

    I.model.loadRivers();
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

    I.model.loadHeads();
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

$( "#btn_calculate_model").click(function () {
    $.ajax({
        type: 'POST',
        url: '/api/modflowmodels/'+I.model.id+'/calculation.json',
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