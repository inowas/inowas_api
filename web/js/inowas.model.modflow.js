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
    if (! I.model.initialized){return}
    hide_all();
    $( "#summary" ).show();
    $( ".summary" ).addClass('active');
    I.model.loadSummary();
});

$( ".area" ).click(function(){
    if (! I.model.initialized){return}
    hide_all();
    $( "#area" ).show();
    $( ".area" ).addClass('active');
    I.model.loadArea();
});

$( ".soilmodel" ).click(function(){
    if (! I.model.initialized){return}
    hide_all();
    $( "#soilmodel" ).show();
    $( ".soilmodel" ).addClass('active');
    I.model.loadSoilmodel();
    //loadLayerImg(I.model.id, 0, 'et', true);
});

$( ".boundaries" ).click(function () {
    if (! I.model.initialized){return}
    hide_all();
    $( "#boundaries" ).show();
    $( ".boundaries" ).addClass('active');

    I.model.loadBoundaries();

});

$( ".wells" ).click(function() {
    if (! I.model.initialized){return}
    hide_all();
    $( "#wells" ).show();
    $( ".boundaries" ).addClass('active');
    $( ".wells" ).addClass('active');

    I.model.loadWells();
});

$( ".rivers" ).click(function() {
    if (! I.model.initialized){return}
    hide_all();
    $( "#rivers" ).show();
    $( ".boundaries" ).addClass('active');
    $( ".rivers" ).addClass('active');

    I.model.loadRivers();
});

$( ".calculation" ).click(function(){
    if (! I.model.initialized){return}
    hide_all();
    $( "#calculation" ).show();
    $( ".calculation" ).addClass('active');

    $.getJSON( "/api/modflowmodels/"+I.model.id+"/contents/calculation.json", function ( data ) {
        $(".content_calculation").html( data.html );
    });

    $.getJSON( "/api/modflowmodels/"+I.model.id+"/calculations.json", function ( data ) {
        console.log(data);

        var message = '';
        if (data.length == 0){
            message = 'Please run the calculation...';
        } else {
            message = data.output.replace(new RegExp('\r?\n','g'), '<br />')
        }

        $('#log').html(message);
    });
});

$( ".results" ).click(function(){
    if (! I.model.initialized){return}
    hide_all();
    $( "#results" ).show();
    $( ".results" ).addClass('active');

    I.model.loadHeads();
});

$( ".history" ).click(function(){
    if (! I.model.initialized){return}
    hide_all();
    $( "#history" ).show();
    $( ".history" ).addClass('active');
});

$( ".delete" ).click(function(){
    if (! I.model.initialized){return}
    hide_all();
    $( "#delete" ).show();
    $( ".delete" ).addClass('active');
});

$(document).on('click', '.btn_calculation', function(event){
        $.post(
            "/api/modflowmodels/"+I.model.id+"/calculations.json",
            function ( data ) {
                $(".calculation").click();
                (function poll() {
                    var calculationData;
                    $.ajax({
                        url: "/api/modflowmodels/"+I.model.id+"/calculations.json",
                        type: "GET",
                        success: function(data) {
                            calculationData = data;
                        },
                        dataType: "json",
                        complete: function() {
                            $.getJSON( "/api/modflowmodels/"+I.model.id+"/contents/calculation.json", function ( data ) {
                                $(".content_calculation").html( data.html );
                            });
                            $('#log').html(calculationData.output.replace(new RegExp('\r?\n','g'), '<br />'));

                            if (calculationData.state < 10){
                                setTimeout(function() {poll()}, 2500)
                            }
                        },
                        timeout: 2000
                    })
                })();
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