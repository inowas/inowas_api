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

    $.getJSON( "/api/modflowmodels/"+modelId+"/contents/summary.json", function ( data ) {
        $(".content_summary").html( data.html );
        var polygon = L.geoJson(jQuery.parseJSON(data.geojson)).addTo(area_map).bindPopup("Groundwater model area Hanoi II.");
        area_map.fitBounds(polygon.getBounds());
    });
});

$( ".soilmodel" ).click(function(){
    hide_all();
    $( "#soilmodel" ).show();
    $( ".soilmodel" ).addClass('active');

    var polygon;
    $.getJSON( "/api/modflowmodels/"+modelId+"/contents/soilmodel.json", function ( data ) {
        $(".content_soilmodel").html( data.html );
        polygon = L.geoJson(jQuery.parseJSON(data.geojson)).addTo(soilmodel_map).bindPopup("Groundwater model area Hanoi II.");
        soilmodel_map.fitBounds(polygon.getBounds());
    });

    loadLayerImg(modelId, 0, 'et', true);
});

$( ".boundaries" ).click(function () {
    hide_all();
    $( "#boundaries" ).show();
    $( ".boundaries" ).addClass('active');
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

