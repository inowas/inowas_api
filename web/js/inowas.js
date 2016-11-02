Array.prototype.clean = function(deleteValue) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == deleteValue) {
            this.splice(i, 1);
            i--;
        }
    }
    return this;
};

// global namespace
var I = {};

I.user = {
    id: null,
    userName: null,
    apiKey: null,
    setApiKey: function (apiKey) {
        this.apiKey = apiKey;
        $.ajaxSetup({
            headers : {'X-AUTH-TOKEN' : apiKey }
        });
    }

};

I.models = {
    locale: 'en-US',
    private: [],
    public: [],
    load: function(){
        $.getJSON( "/api/modflowmodels.json", function ( data ) {
            I.models.renderModels( data, '#table_public_models' );
            $.each(data, function (key, value) {
                I.models.public.push(value);
            });
        });

        $.getJSON( "/api/users/"+ I.user.userName +"/modflowmodels.json", function ( data ) {
            I.models.renderModels( data, '#table_public_models' );
            $.each(data, function (key, value) {
                I.models.private.push(value);
            });
        })
    },
    renderModels: function ( data, id ) {
        var html = '' +
            '<thead>' +
            '<tr>' +
            '<th>#</th>' +
            '<th>Model name</th> ' +
            '<th>Creator</th>' +
            '<th>Created</th>' +
            '<th>Last modified</th>' +
            '<th>Public</th>' +
            '<th>State</th>' +
            '</tr>' +
            '</thead>' +
            '<tbody >';

        $.each(data, function (key, value) {
            html += '<tr id="model_'+ value.id +'" class="model_list_item">';
            html += '<td>Asia</td>';
            html += '<td>'+ value.name +'</td>';
            html += '<td>'+ value.owner.username +'</td>';
            html += '<td>'+ I.models.stringToDate(value.date_created) +'</td>';
            html += '<td>'+ I.models.stringToDate(value.date_modified) +'</td>';
            html += '<td>'+ (value.public==true?'yes':'no') +'</td>';
            html += '<td>---</td>';
            html += '</tr>';
        });

        html += '</tbody>';

        $( id ).html(html);

        $('.model_list_item').hover(function() {
            var modelId = this.id.split("_")[1];
            I.models.loadInfoBox(modelId);
        }).click(function() {
            var modelId = this.id.split("_")[1];
            var model = I.models.findModelById( modelId );

            I.model.clear( true );
            I.model.name = model.name;
            I.model.description = model.description;
            I.model.boundingBox = model.bounding_box;
            I.model.gridSize = model.grid_size;
            I.model.initialize(modelId, true);
            $('#models_label').click();
        });
    },
    loadInfoBox: function (modelId) {
        var model = I.models.findModelById( modelId );

        if (model !== null){
            var html = '';
            html += '<div class="panel-body">';
            html += '    <div class="row">';
            html += '        <div class="col-md-4">';
            html += '            <img class="image img-responsive" src="/api/modflowmodels/'+ model.id +'/image.jpg" />';
            html += '        </div>';
            html += '        <div id="testimage"></div>';
            html += '        <div class="col-md-8">';
            html += '            <div class="row">';
            html += '                <div class="col-md-4">Model name:</div>';
            html += '                <div class="col-md-8">'+ model.name +'</div>';
            html += '            </div>';
            html += '            <div class="row">';
            html += '                <div class="col-md-4">Description:</div>';
            html += '                <div class="col-md-8">'+ model.description +'</div>';
            html += '            </div>';
            html += '            <div class="row">';
            html += '                <div class="col-md-4">Creator</div>';
            html += '                <div class="col-md-8">'+ model.owner.username +'</div>';
            html += '            </div>';
            html += '            <div class="row">';
            html += '                <div class="col-md-4">Created:</div>';
            html += '                <div class="col-md-8">'+ I.models.stringToDate(model.date_created) +'</div>';
            html += '            </div>';
            html += '            <div class="row">';
            html += '                <div class="col-md-4">Modified on:</div>';
            html += '                <div class="col-md-8">'+ I.models.stringToDate(model.date_modified) +'</div>';
            html += '            </div>';
            html += '            <div class="row">';
            html += '                <div class="col-md-4">Surface area:</div>';
            html += '                <div class="col-md-8"></div>';
            html += '            </div>';
            html += '            <div class="row">';
            html += '                <div class="col-md-4">Status:</div>';
            html += '                <div class="col-md-8">'+ 'Status' +'</div>';
            html += '            </div>';
            html += '        </div>';
            html += '    </div>';
            html += '</div>';

            $('#model_info').html(html);
        }
    },
    findModelById: function (id) {
        var model = null;

        if (this.public !== null){
            $.each(this.public, function (key, value) {
                if (value.id === id){
                    model = value;
                    return false;
                }
            });
        }

        if (this.private !== null) {
            $.each(this.private, function (key, value) {
                if (value.id === id) {
                    model = value;
                    return false;
                }
            });
        }

        return model;
    },
    stringToDate: function ( text ) {
        return new Date(text).toLocaleString(this.locale);
    }
};

I.model = {
    baseModelId: null,
    id: null,
    name: null,
    description: null,
    initialized: false,
    boundingBox: null,
    gridSize: null,
    activeCellsGridLayer: null,
    boundingBoxLayer: null,
    map: null,
    wellsLayer: null,
    scenarios: null,
    data: {
        area: null,
        soilmodel: null,
        riv: null,
        chb: null,
        ghb: null,
        rch: null,
        wel: null
    },
    area: {},
    boundaries: {
        riv: null,
        chb: null,
        ghb: null,
        rch: null,
        wel: null
    },
    heads: null,
    content: {},
    maps: {
        area: null,
        boundaries: null,
        riv: null,
        chb: null,
        ghb: null,
        rch: null,
        wel: null,
        summary: null,
        heads: null
    },
    buttons: {
        updateActiveCells: null
    },
    styles: {
        inactive: {color: "#000", weight: 0, fillColor: "#000", fillOpacity: 0.7},
        active: {color: "#ff7800", weight: 0, fillColor: "#000", fillOpacity: 0},
        boundingBox: {color: "#000", weight: 0.5, fillColor: "blue", fillOpacity: 0},
        areaGeometry: {color: "#000", weight: 0.5, fillColor: "blue", fillOpacity: 0.1},
        hasNoWell: {color: "#000", weight: 0, fillOpacity: 0},
        hasWell: {color: "blue", weight: 1, fillColor: "darkblue", fillOpacity: 1},
        wells: {
            cw:  {radius: 5, color: 'black', weight: 1, fillColor: 'darkgreen', fillOpacity: 0.7},
            iw:  {radius: 5, color: 'black', weight: 1, fillColor: 'darkgreen', fillOpacity: 0.7},
            sniw:  {radius: 7, color: 'red', weight: 2, fillColor: 'darkgreen', fillOpacity: 0.7},
            puw: {radius: 5, color: 'black', weight: 1, fillColor: 'darkblue', fillOpacity: 0.7},
            snpw:  {radius: 7, color: 'red', weight: 2, fillColor: 'darkblue', fillOpacity: 0.7},
            prw: {radius: 5, color: 'black', weight: 1, fillColor: 'darkblue', fillOpacity: 0.7},
            smw: {radius: 7, color: 'black', weight: 1, fillColor: 'red', fillOpacity: 1},
            snw: {radius: 7, color: 'black', weight: 1, fillColor: 'yellow', fillOpacity: 1},
            snifw:  {radius: 7, color: '#63b3ea', weight: 2, fillColor: '#bbdff6', fillOpacity: 0.7}
        },
        river: {color: "#000", weight: 0.5, fillColor: "blue", fillOpacity: 0}
    },
    initialize: function(id, isBaseModel){
        if (isBaseModel){
            I.model.baseModelId = id;
        }
        I.model.id = id;
        I.model.map = L.map('map', {
            zoomControl: false
        }).fitBounds(this.createBoundingBoxPolygon(this.boundingBox).getBounds());
        L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(I.model.map);

        $(window).on("resize", function() {
            $("#map").height($(window).height()).width($(window).width());
            I.model.map.invalidateSize();
        }).trigger("resize");

        $("#model_name").text(I.model.name);
        $("#model_description ").text(I.model.description);

        var overlayMaps = {};
        overlayMaps['Bounding Box'] = I.model.createBoundingBoxLayer(I.model.boundingBox);

        $.when(

            $.getJSON( "/api/modflowmodels/"+this.id+"/area.json", function ( data ) {
                overlayMaps['Area'] = L.geoJson($.parseJSON(data.geojson), I.model.styles.areaGeometry).addTo(I.model.map);
                I.model.map.fitBounds(overlayMaps['Area']);
                I.model.data.area = data;
            }),

            $.getJSON( "/api/modflowmodels/"+this.id+"/wells.json", function ( data ) {
                overlayMaps['Wells'] = I.model.createWellsLayer(data).addTo(I.model.map);
                overlayMaps['Wells active Cells'] = I.model.createWellsActiveCellsLayer(data, I.model.boundingBox, I.model.gridSize);
                I.model.data.wel = data;

                $("#wells_badge").text(I.model.getNumberOf(data));
            }),

            $.getJSON( "/api/modflowmodels/"+this.id+"/rivers.json", function ( data ) {
                overlayMaps['Rivers'] = I.model.createRiversLayer(data).addTo(I.model.map);
                overlayMaps['Rivers active cells'] = I.model.createRiversActiveCellsLayer(data, I.model.boundingBox, I.model.gridSize);
                I.model.data.riv = data;

                $("#rivers_badge").text(I.model.getNumberOf(data));
            }),

            $.getJSON( "/api/modflowmodels/"+this.id+"/scenarios.json", function ( data ) {
                I.model.renderScenarios(data);
                I.model.scenarios = data;
            }),

            $.getJSON( "/api/modflowmodels/"+this.id+"/heads.json", function ( data ) {
                if (! $.isArray(data)) {
                    I.model.heads = data;
                }
            })
        ).then(function(){

            if (I.model.heads !== null){
                var bounds = [[I.model.boundingBox.y_min, I.model.boundingBox.x_min],[I.model.boundingBox.y_max, I.model.boundingBox.x_max]];
                overlayMaps['Heads'] = L.imageOverlay('/api/modflowmodels/'+I.model.id+'/heads/image.png', bounds, { opacity: 0.5, position: 'back' }).addTo(I.model.map).bringToBack();
            }

            L.control.layers({}, overlayMaps).addTo(I.model.map);
            L.control.zoom({position:'topright'}).addTo(I.model.map);

            L.easyButton({
                id: 'center-model',
                position: 'topright',
                type: 'replace',
                leafletClasses: true,
                states:[{
                    stateName: 'get-center',
                    onClick: function(button, map){
                        I.model.map.fitBounds(overlayMaps['Area'].getBounds());
                    },
                    title: 'Show me the model',
                    icon: 'fa-crosshairs'
                }]
            }).addTo( I.model.map );


            $("#recharge_badge").text(I.model.getNumberOf(I.model.data.rch));
            $("#constant_head_badge").text(I.model.getNumberOf(I.model.data.chb));
            $("#general_head_badge").text(I.model.getNumberOf(I.model.data.ghb));

            $("#sidebar").show();
            $('#toolbox').on('mouseover mousedown touchstart', function() {
                I.model.disableMap();
            }).on('mouseout mouseup touchend', function() {
                I.model.enableMap();
            });
            $("#results_label").show();

            I.model.initialized = true;
        });
    },
    initializeWithoutModel: function () {
        I.model.map = L.map('map', {
            zoomControl: false
        }).setView([50.9661, 13.92367], 5);

        L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(I.model.map);

        $(window).on("resize", function() {
            $("#map").height($(window).height()).width($(window).width());
            I.model.map.invalidateSize();
            $("#results").height($(window).height());
        }).trigger("resize");

        $('#models_label').click();
    },
    clear: function ( isBaseModel ) {
        I.model.initialized = false;
        I.model.name = null;
        I.model.description = null;
        I.model.initialized = false;
        I.model.boundingBox = null;
        I.model.gridSize = null;
        I.model.activeCellsGridLayer = null;
        I.model.boundingBoxLayer = null;
        I.model.wellsLayer = null;
        I.model.map.remove();
        I.model.map = null;

        if ( isBaseModel ){
            I.model.scenarios = null;
        }
    },
    initializeMapImage: function(id){
        I.model.id = id;
        I.model.map = L.map('map', {
            zoomControl: false
        }).setView([50.9661, 13.92367], 5);

        L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(I.model.map);

        $("#map").height(500).width(500);
        I.model.map.invalidateSize();

        $.when(
            $.getJSON( "/api/modflowmodels/"+id+".json", function ( data ) {
                I.model.boundingBox = data.bounding_box;
                I.model.gridSize = data.grid_size;
            }),

            $.getJSON( "/api/modflowmodels/"+this.id+"/area.json", function ( data ) {
                I.model.data.area = data;
            }),

            $.getJSON( "/api/modflowmodels/"+this.id+"/wells.json", function ( data ) {
                I.model.data.wel = data;
            }),

            $.getJSON( "/api/modflowmodels/"+this.id+"/rivers.json", function ( data ) {
                I.model.data.riv = data;
            }),

            $.getJSON( "/api/modflowmodels/"+this.id+"/scenarios.json", function ( data ) {
                I.model.scenarios = data;
            }),
            $.getJSON( "/api/modflowmodels/"+this.id+"/heads.json", function ( data ) {
                I.model.heads = data;
            })
        ).then(function(){
            if (I.model.heads.length > 0){
                var heads = I.model.getLayerOfLastHead( I.model.heads).addTo(I.model.map);
            }

            var area = L.geoJson($.parseJSON(I.model.data.area.geojson), I.model.styles.areaGeometry).addTo(I.model.map);
            var wells = I.model.createWellsLayer(I.model.data.wel).addTo(I.model.map);
            var rivers = I.model.createRiversLayer(I.model.data.riv).addTo(I.model.map);
            I.model.map.fitBounds(area.getBounds());
            I.model.initialized = true;
            $("#app").addClass("initialized");
        });
    },
    disableMap: function() {
        I.model.map.dragging.disable();
        I.model.map.touchZoom.disable();
        I.model.map.doubleClickZoom.disable();
    },
    enableMap: function() {
        I.model.map.dragging.enable();
        I.model.map.touchZoom.enable();
        I.model.map.doubleClickZoom.enable();
    },
    getNumberOf: function( data ){
        if (data == undefined){
            return 0;
        }

        var number = 0;
        for (var property in data) {
            if (data.hasOwnProperty(property)) {
                if (data[property] instanceof Array){
                    number += data[property].length;
                } else {
                    number ++;
                }
            }
        }
        return number;
    },
    renderScenarios: function( scenarios ){
        var html='<div class="list-group-item list-group-item-action"></div>';
        html+=
            '<a href="#" id="scenario_'+ I.model.id +'" class="list-group-item list-group-item-action scenario_list_item base_model active"> \
                <div class="row">\
                    <div class="col-sm-4 image">\
                        <img src="/api/modflowmodels/'+I.model.id+'/image.jpg" class="img-responsive" alt="'+I.model.name+'">\
                        </div> \
                        <div class="col-sm-8">\
                            <h1>'+I.model.name+'</h1>\
                            <p>'+I.model.description+'</p>\
                        </div>\
                    </div>\
                </a>';
        $.each(scenarios , function (index, value){
            html+=
                '<a href="#" id="scenario_'+ value.id +'" class="list-group-item list-group-item-action scenario_list_item"> \
                    <div class="row">\
                        <div class="col-sm-4 image">\
                            <img src="/api/modflowmodels/'+value.id+'/image.jpg" class="img-responsive" alt="'+value.name+'">\
                        </div> \
                        <div class="col-sm-8">\
                            <h1>'+value.name+'</h1>\
                            <p>'+value.description+'</p>\
                        </div>\
                    </div>\
                </a>';
        });
        $('#scenarios_list').html(html);
        $('.scenario_list_item').click(function() {
            if (I.model.initialized){
                $( ".scenario_list_item" ).each(function( index ) {
                    $(this).removeClass('active');
                });
                $(this).addClass('active');
                I.model.clear( false );
                I.model.initialize(this.id.split("_")[1], false);
                $('#scenarios').find('.flip').click();
            }
        });
    },
    getStyle: function (type, value){
        if (type == 'area'){
            if (value == true){
                return this.styles.active;
            } else {
                return this.styles.inactive;
            }
        }

        if (type == 'wells'){
            if (value == true){
                return this.styles.hasWell;
            } else {
                return this.styles.hasNoWell;
            }
        }
    },
    updateProperties: function (id) {
        $.ajax({
            type: 'PUT',
            url: '/api/modflowmodels/'+id+'.json',
            data: { 'active_cells' : JSON.stringify(I.model.activeCells.cells) },
            statusCode: {
                200: function( data ) {
                    that.activeCells = data.active_cells;
                    that.boundingBox = data.bounding_box;
                    that.gridSize = data.grid_size;
                    that.buttons.updateActiveCells.disable();
                }
            }
        });

        return true;
    },
    updateRiver: function () {
        var rivers = this.data.riv;
        for(var rKey in rivers){
            if (! rivers.hasOwnProperty(rKey)) continue;

            var river = rivers[rKey];

            var data = null;
            if (river.updateGeometry) {
                data = {'latLngs': JSON.stringify(river.latLngs)};
            }

            if (river.updateActiveCells) {
                data = {'activeCells': JSON.stringify(river.active_cells.cells)};
            }

            if (data) {
                $.ajax({
                    type: 'PUT',
                    url: '/api/modflowmodels/'+this.id+'/rivers/'+river.id+'.json',
                    data: data,
                    statusCode: {
                        200: function( data ) {
                            that.data.riv = data;
                            that.loadRivers(true);
                        }
                    }
                })
            }
        }

        return true;
    },
    updateWells: function () {
        var allWells = this.data.wel;
        for(var wellsTypeKey in allWells){
            if (! allWells.hasOwnProperty(wellsTypeKey)) continue;
            var wells = allWells[wellsTypeKey];

            for(var wellsKey in wells) {
                if (!wells.hasOwnProperty(wellsKey)) continue;
                var well = wells[wellsKey];

                var data = null;
                if (well.updateGeometry) {
                    data = {'latLng': JSON.stringify(well.latLng)};
                }

                if (data) {
                    $.ajax({
                        type: 'PUT',
                        url: '/api/modflowmodels/' + this.id + '/wells/' + well.id + '.json',
                        data: data,
                        statusCode: {
                            200: function (data) {
                                $.getJSON("/api/modflowmodels/" + I.model.id + "/wells.json", function (data) {
                                    I.model.data.wel = data;
                                    I.model.loadWells(true);
                                });
                            }
                        }
                    });
                }
            }
        }

        return true;
    },
    loadSummary: function (refresh) {
        var that = this;
        if (this.maps.summary == null || refresh == true){
            if (refresh == true){
                this.maps.summary.remove();
            }

            var map = this.maps.summary = this.createBaseMap('map-summary');
            map.touchZoom.disable();
            map.doubleClickZoom.disable();
            map.scrollWheelZoom.disable();
            map.boxZoom.disable();
            map.keyboard.disable();

            this.createAreaLayer().addTo(map);
            map.fitBounds(this.createBoundingBoxPolygon(this.boundingBox).getBounds());


            $.get( "/api/modflowmodels/"+I.model.id+".html", function ( data ) {
                that.content.summary =  data;
                $(".content_summary").html( that.content.summary );
            });

            this._loadAndAddWells( map, false );
        }
    },
    loadArea: function (refresh) {
        if (this.maps.area == null || refresh == true) {
            if (refresh == true){
                this.maps.area.remove();
            }

            var boundingBox = this.createBoundingBoxLayer(this.boundingBox).addTo(map);
            var areaPolygon = L.geoJson($.parseJSON(this.data.area.geojson), this.styles.areaGeometry).addTo(map);
            var areaActiveCells = this.createAreaActiveCellsLayer(this.data.area.active_cells, this.boundingBox, this.gridSize, this.data.area.mutable);
            map.fitBounds(this.createBoundingBoxPolygon(this.boundingBox).getBounds());

            var baseMaps = {};
            var overlayMaps = {"Area": areaPolygon, "Bounding Box": boundingBox, "Inactive Cells": areaActiveCells};
            L.control.layers(baseMaps, overlayMaps).addTo(map);

            if (this.data.area.mutable){
                this.buttons.updateActiveCells = L.easyButton('fa-save', function(btn, map){
                    I.model.updateProperties( I.model.id );
                }).disable().addTo(map);
            }

            this.maps.area = map;
        }
    },
    loadSoilmodel: function(refresh) {
        if (this.maps.soilmodel == null || refresh == true) {
            if (refresh == true){
                this.maps.soilmodel.remove();
            }

            var map = this.createBaseMap( 'soilmodel-map' );
            var boundingBox = this.createBoundingBoxLayer(this.boundingBox).addTo(map);
            var areaPolygon = L.geoJson($.parseJSON(this.data.area.geojson), this.styles.areaGeometry).addTo(map);
            map.fitBounds(this.createBoundingBoxPolygon(this.boundingBox).getBounds());

            $.get( "/api/modflowmodels/"+I.model.id+"/soilmodel.html", function ( data ) {
                $(".content_soilmodel").html( data );
            });
        }
    },
    loadBoundaries: function(refresh) {
        if (this.maps.boundaries == null || refresh == true) {
            if (refresh == true && this.maps.boundaries != null){
                this.maps.boundaries.remove();
            }

            var map = this.createBaseMap( 'boundaries-map' );

            var boundingBox = this.createBoundingBoxLayer(this.boundingBox).addTo(map);
            var areaPolygon = L.geoJson(jQuery.parseJSON(this.data.area.geojson), this.styles.areaGeometry).addTo(map);
            map.fitBounds(this.createBoundingBoxPolygon(this.boundingBox).getBounds());
            var wells = this.createWellsLayer(this.data.wel).addTo(map);
            var rivers = this.createRiversLayer(this.data.riv).addTo(map);

            var baseMaps = {};
            var overlayMaps = {"Wells": wells, "Rivers": rivers};
            L.control.layers(baseMaps, overlayMaps).addTo(map);

            this.maps.boundaries = map;
        }
    },
    loadWells: function (refresh) {
        if (this.maps.wel == null || refresh == true) {
            if (refresh == true && this.maps.wel != null){
                this.maps.wel.remove();
            }

            var map = this.createBaseMap( 'wells-map' );
            var boundingBox = this.createBoundingBoxLayer(this.boundingBox).addTo(map);
            var areaPolygon = L.geoJson(jQuery.parseJSON(this.data.area.geojson), this.styles.areaGeometry).addTo(map);
            map.fitBounds(this.createBoundingBoxPolygon(this.boundingBox).getBounds());
            var wells = this.createWellsLayer(this.data.wel).addTo(map);
            var wellsActiveCells = this.createWellsActiveCellsLayer(this.data.wel, this.boundingBox, this.gridSize);

            var baseMaps = {};
            var overlayMaps = {"Wells": wells, "Active cells": wellsActiveCells};
            L.control.layers(baseMaps, overlayMaps).addTo(map);

            var drawnItems = new L.FeatureGroup();
            wells.eachLayer(function (layer) {
                if (layer.raw.mutable)(
                    layer.addTo(drawnItems)
                )
            });

            if (drawnItems.getLayers().length > 0){
                var drawControlEditOnly = new L.Control.Draw({
                    edit: {
                        featureGroup: drawnItems,
                        remove: false
                    },
                    draw: false
                });

                drawControlEditOnly.addTo(map);
                map.on("draw:edited", function (e) {
                    var layers = e.layers;

                    layers.eachLayer(function (layer) {
                        layer.raw.latLng = layer.getLatLng();
                        layer.raw.updateGeometry = true;
                    });
                    that.updateWells();
                });
            }


            this.maps.wel = map;
        }
    },
    loadRivers: function (refresh) {
        if (this.maps.riv == null || refresh == true) {
            if (refresh == true && this.maps.riv != null){
                this.maps.riv.remove();
            }

            var map = this.createBaseMap( 'rivers-map' );
            var boundingBox = this.createBoundingBoxLayer(this.boundingBox).addTo(map);
            var areaPolygon = L.geoJson(jQuery.parseJSON(this.data.area.geojson), this.styles.areaGeometry).addTo(map);
            map.fitBounds(this.createBoundingBoxPolygon(this.boundingBox).getBounds());
            var rivers = this.createRiversLayer(this.data.riv).addTo(map);
            var riversActiveCells = this.createRiversActiveCellsLayer(this.data.riv, this.boundingBox, this.gridSize);

            var baseMaps = {};
            var overlayMaps = {"Wells": rivers, "Active cells": riversActiveCells};
            L.control.layers(baseMaps, overlayMaps).addTo(map);

            var drawnItems = new L.FeatureGroup();
            rivers.eachLayer(function (layer) {
                if (layer.raw.mutable)(
                    layer.addTo(drawnItems)
                )
            });

            if (drawnItems.getLayers().length > 0){
                var drawControlEditOnly = new L.Control.Draw({
                    edit: {
                        featureGroup: drawnItems,
                        remove: false
                    },
                    draw: false
                });

                drawControlEditOnly.addTo(map);

                map.on("draw:edited", function (e) {
                    var layers = e.layers;
                    layers.eachLayer(function (layer) {
                        layer.raw.latLngs = layer.getLatLngs();
                        layer.raw.updateGeometry = true;
                    });
                    that.updateRiver( I.model.id );
                });
            }

            this.maps.riv = map;
        }
    },
    loadHeads: function (refresh) {
        if (this.maps.heads == null || refresh == true) {
            if (refresh == true && this.maps.heads != null){
                this.maps.heads.remove();
            }

            var map = this.createBaseMap( 'heads-map' );
            L.geoJson(jQuery.parseJSON(this.data.area.geojson), this.styles.areaGeometry).addTo( map );
            map.fitBounds(this.createBoundingBoxPolygon(this.boundingBox).getBounds());
            this._loadAndAddHeads( map );
        }
    },
    createBaseMap: function (id, options) {
        var map = new L.map( id, options );
        L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {
            maxZoom: 18,
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
            '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
            'Imagery © <a href="http://mapbox.com">Mapbox</a>',
            id: 'mapbox.streets'
        }).addTo(map);
        map.addControl( new L.Control.FullScreen({
            forceSeparateButton: true
        }));
        return map;
    },
    createAreaLayer: function() {
        return L.geoJson(jQuery.parseJSON(this.data.area.geojson), this.styles.areaGeometry);
    },
    createAreaActiveCellsLayer: function (activeCells, boundingBox, gridSize, mutable) {

        var layers = new L.FeatureGroup();
        var dx = (boundingBox.x_max - boundingBox.x_min) / gridSize.n_x;
        var dy = (boundingBox.y_max - boundingBox.y_min) / gridSize.n_y;

        for (var row=0; row<gridSize.n_y; row++){
            for (var col=0; col<gridSize.n_x; col++){
                var bb = {};
                bb.x_min = boundingBox.x_min + col*dx;
                bb.x_max = boundingBox.x_min + col*dx+dx;
                bb.y_min = boundingBox.y_max - row*dy-dy;
                bb.y_max = boundingBox.y_max - row*dy;

                var value = true;
                if (activeCells.cells[row] == undefined || activeCells.cells[row][col] == false){
                    value = false;
                }

                var rectangle = this.createRectangle(bb, this.getStyle('area', value));
                rectangle.col = col;
                rectangle.row = row;

                if (mutable){
                    rectangle.on('click', function(e) {

                        if (activeCells.cells[e.target.row] == undefined || activeCells.cells[e.target.row][e.target.col] == false){
                            activeCells.cells[e.target.row][e.target.col] = true;
                            e.target.setStyle(that.getStyle('area', activeCells.cells[e.target.row][e.target.col]));
                        } else {
                            activeCells.cells[e.target.row][e.target.col] = false;
                            e.target.setStyle(that.getStyle('area', false));
                        }

                        that.buttons.updateActiveCells.enable();
                    });
                }
                rectangle.addTo(layers);
            }
        }

        return layers;
    },
    createWellsLayer: function (wells) {
        var layer = new L.LayerGroup();
        for (var key in wells) {
            if (!wells.hasOwnProperty(key)) continue;
            var items = wells[key];
            items.forEach(function (item) {
                var popupContent = '<h4>' + item.name + '</h4>';
                if (item.stress_periods.length>0 && item.stress_periods[0].hasOwnProperty('flux')){
                    popupContent += '<p>Flux: ' + item.stress_periods[0].flux +  ' m<sup>3</sup>/day</p>';
                }
                var well = L.circleMarker(L.latLng(item.point.y, item.point.x), I.model.styles.wells[key]).bindPopup(popupContent).addTo(layer);
                well.raw = item;
            });
        }

        return layer;
    },
    createWellsActiveCellsLayer: function (wells, boundingBox, gridSize) {
        var layers = new L.FeatureGroup();
        var dx = (boundingBox.x_max - boundingBox.x_min) / gridSize.n_x;
        var dy = (boundingBox.y_max - boundingBox.y_min) / gridSize.n_y;

        for (var key in wells) {
            if (!wells.hasOwnProperty(key)) continue;
            var items = wells[key];

            items.forEach(function (item) {
                for(var nRow in item.active_cells.cells) {

                    if (!item.active_cells.cells.hasOwnProperty(nRow)){
                        continue;
                    }

                    var row = item.active_cells.cells[nRow];

                    for(var nCol in row) {
                        if (!row.hasOwnProperty(nCol)){continue;}

                        var bb = {};
                        bb.x_min = boundingBox.x_min + nCol*dx;
                        bb.x_max = boundingBox.x_min + nCol*dx+dx;
                        bb.y_min = boundingBox.y_max - nRow*dy-dy;
                        bb.y_max = boundingBox.y_max - nRow*dy;

                        var rectangle = I.model.createRectangle(bb, I.model.getStyle('wells', true));
                        rectangle.addTo(layers);
                    }
                }
            });
        }

        return layers;
    },
    createRiversLayer: function(rivers) {
        var layers = new L.LayerGroup();
        for (var rivKey in rivers) {
            if (!rivers.hasOwnProperty(rivKey)) continue;
            var riverLayer = L.geoJson($.parseJSON(rivers[rivKey].geojson), this.styles);

            var raw = rivers[rivKey];
            riverLayer.eachLayer(function(layer){
                var popupContent = '<h4>' + raw.name + '</h4>';
                popupContent += '<p>Bottom: ' + raw.stress_periods[0].rbot +  ' m<br/>';
                popupContent += 'Stage: ' + raw.stress_periods[0].stage +  ' m<br/>';
                popupContent += 'Cond: ' + raw.stress_periods[0].cond +  ' m/day</p>';

                layer.raw = raw;
                layer.bindPopup(popupContent).addTo(layers)
            });
        }

        return layers;
    },
    createRiversActiveCellsLayer: function(rivers, boundingBox, gridSize){
        var layers = new L.FeatureGroup();
        var dx = (boundingBox.x_max - boundingBox.x_min) / gridSize.n_x;
        var dy = (boundingBox.y_max - boundingBox.y_min) / gridSize.n_y;

        for (var key in rivers) {
            if (!rivers.hasOwnProperty(key)) continue;

            var activeCells = rivers[key].active_cells.cells;
            for(var nRow in activeCells) {

                if (!activeCells.hasOwnProperty(nRow)){
                    continue;
                }

                var row = activeCells[nRow];

                for(var nCol in row) {
                    if (!row.hasOwnProperty(nCol)){continue;}

                    var bb = {};
                    bb.x_min = boundingBox.x_min + nCol*dx;
                    bb.x_max = boundingBox.x_min + nCol*dx+dx;
                    bb.y_min = boundingBox.y_max - nRow*dy-dy;
                    bb.y_max = boundingBox.y_max - nRow*dy;

                    var rectangle = I.model.createRectangle(bb, I.model.getStyle('wells', true));
                    rectangle.addTo(layers);
                }
            }
        }

        return layers;
    },
    createBoundingBoxLayer: function( boundingBox ) {
        var layer = new L.LayerGroup();
        this.createBoundingBoxPolygon(boundingBox).addTo(layer);
        this.boundingBoxLayer = layer;
        return layer;
    },
    createBoundingBoxPolygon: function( boundingBox ) {
        return this.createRectangle(boundingBox, this.styles.boundingBox);
    },
    createRectangle: function( boundingBox, style ){
        return new L.Rectangle([[boundingBox.y_min, boundingBox.x_min], [boundingBox.y_max, boundingBox.x_max]], style);
    },
    createHeatMap: function (heads, min, max, boundingBox, gridSize, layerGroup) {
        var dx = (boundingBox.x_max - boundingBox.x_min) / gridSize.n_x;
        var dy = (boundingBox.y_max - boundingBox.y_min) / gridSize.n_y;

        for (var row=0; row<gridSize.n_y; row++){
            for (var col=0; col<gridSize.n_x; col++) {
                var bb = {};
                bb.x_min = boundingBox.x_min + col * dx;
                bb.x_max = boundingBox.x_min + col * dx + dx;
                bb.y_min = boundingBox.y_max - row * dy - dy;
                bb.y_max = boundingBox.y_max - row * dy;

                if (heads[row] === undefined || heads[row][col] === undefined || heads[row][col] === null) {
                    continue;
                }

                var value = heads[row][col];
                value = Math.round(value * 100) / 100;

                var rectangle = I.model.createRectangle(
                    bb,
                    {color: "blue", weight: 0, fillColor: I.model.getColor(min, max, value), fillOpacity: 0.3}
                );

                rectangle.addTo(layerGroup);
            }
        }

        return layerGroup;
    },
    createHeadsLayer: function (heads, min, max, time, boundingBox, gridSize, layerGroup, info, map) {

        var lay = 0;
        heads = heads[lay];

        var dx = (boundingBox.x_max - boundingBox.x_min) / gridSize.n_x;
        var dy = (boundingBox.y_max - boundingBox.y_min) / gridSize.n_y;

        for (var row=0; row<gridSize.n_y; row++){
            for (var col=0; col<gridSize.n_x; col++){
                var bb = {};
                bb.x_min = boundingBox.x_min + col*dx;
                bb.x_max = boundingBox.x_min + col*dx+dx;
                bb.y_min = boundingBox.y_max - row*dy-dy;
                bb.y_max = boundingBox.y_max - row*dy;

                var value = false;
                if (heads[row] != undefined || heads[row][col] != undefined){
                    value = heads[row][col];
                    value = Math.round(value*100)/100;
                }

                var rectangle = I.model.createRectangle(
                    bb,
                    {color: "blue", weight: 0, fillColor: I.model.getColor(min, max, value), fillOpacity: 0.2, time: time}
                );

                rectangle.time = time;
                rectangle.col = col;
                rectangle.row = row;
                rectangle.lay = lay;
                rectangle.value = value;
                rectangle.on('click', function(e) {

                    map.eachLayer(function(layer){
                        if (layer.marker){
                            map.removeLayer(layer);
                        }
                    });

                    var bb = {};
                    bb.x_min = boundingBox.x_min;
                    bb.x_max = boundingBox.x_max;
                    bb.y_min = boundingBox.y_max - e.target.row*dy-dy;
                    bb.y_max = boundingBox.y_max - e.target.row*dy;

                    var rect = that.createRectangle(bb, {color: "blue", weight: 0, fillColor: 'grey', fillOpacity: 0.5});
                    rect.marker = true;


                    rect.addTo(map).bringToFront();

                    var allHeads = $.extend({}, that.heads);
                    var keys = Object.keys(allHeads);
                    info.update(e.target.value, min, max);

                    // Generate timeseries Graph only if more then one time is given.
                    if (keys.length > 1){
                        var datesColumn = ['x'];
                        var dataColumn = ['data1'];
                        for (var i=0; i<keys.length-1; i++){
                            datesColumn.push(keys[i]);
                            var head = allHeads[keys[i]];
                            head = $.parseJSON(head);
                            dataColumn.push(head[e.target.lay][e.target.row][e.target.col]);
                        }

                        var chart_ts = c3.generate({
                            bindto: '#chart_ts',
                            data: {
                                x: 'x',
                                columns: [
                                    datesColumn,
                                    dataColumn
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

                        $('.chart_ts').show();

                    }

                    var heads = allHeads[e.target.time];
                    heads = $.parseJSON(heads);

                    // Data for each layer
                    for (var nLay=0; nLay<heads.length; nLay++) {
                        var rowData = ['GW-Head'];
                        for (var nCol = 0; nCol<heads[nLay][0].length; nCol++){
                            rowData.push(heads[nLay][e.target.row][nCol])
                        }
                    }

                    var chart_rows = c3.generate({
                        padding: {
                            top: 0,
                            right: 100,
                            bottom: 0,
                            left: 50
                        },
                        bindto: '#chart_rows',
                        data: {
                            columns: [
                                rowData
                            ]
                        },
                        legend: {
                            position: 'top'
                        }
                    });
                    $('.chart_rows').show();
                });

                //rectangle.bindPopup("Groundwater Head: "+value);
                rectangle.addTo(layerGroup);
            }
        }
        return layerGroup;
    },
    getColor: function(min, max, value){
        var heatmap = new Rainbow();
        heatmap.setSpectrum('red', 'yellow', 'lime', 'aqua', 'blue');
        heatmap.setNumberRange(min, max);
        return '#'+heatmap.colorAt(value);
    },
    _loadAndAddWells: function( map, addActiveCells ){
        if (this.boundaries.wel !== null) {
            this._addWellsLayer( this.boundaries.wel, map, addActiveCells );
        } else {
            $.getJSON( "/api/modflowmodels/"+this.id+"/wells.json?srid=4326", function ( data ) {
                that.boundaries.wel = data;
                that._addWellsLayer( data, map, addActiveCells );
            });
        }
    },
    _loadAndAddHeads: function( map ){
        if (this.heads !== null) {
            this._addHeadsLayer( this.heads, map );
        } else {
            $.getJSON( "/api/modflowmodels/"+this.id+"/heads.json", function ( data ) {
                that.heads = data;
                that._addHeadsLayer( data, map );
            });
        }
    },
    _addWellsLayer: function ( wells, map , addActiveCells){
        var geographyLayer = new L.LayerGroup();
        var active_cells = {};
        active_cells.cells = [];

        for (var key in wells) {
            if (!wells.hasOwnProperty(key)) continue;
            var items = wells[key];

            items.forEach(function (item) {
                L.circleMarker([item.point.y, item.point.x], I.model.styles.wells[key]).addTo(geographyLayer);

                if (addActiveCells == true){
                    for(var rowProperty in item.active_cells.cells) {

                        if (!item.active_cells.cells.hasOwnProperty(rowProperty)){continue;}

                        if (active_cells.cells[rowProperty] == null) {
                            active_cells.cells[rowProperty] = [];
                        }

                        var row = item.active_cells.cells[rowProperty];

                        for(var colProperty in row) {
                            if (!row.hasOwnProperty(colProperty)){continue;}
                            active_cells.cells[rowProperty][colProperty] = row[colProperty];
                        }
                    }
                }
            });
        }

        geographyLayer.addTo(map);

        if (addActiveCells == true) {
            var activeCellsLayer = this.createWellsActiveCellsLayer(active_cells, this.boundingBox, this.gridSize);
            activeCellsLayer.addTo(map);
            var baseMaps = {};
            var overlayMaps = {"Wells": geographyLayer, "Active Cells": activeCellsLayer};
            L.control.layers(baseMaps, overlayMaps).addTo(map);
        }
    },
    getLayerOfLastHead: function ( data ) {
        var dates = Object.keys(data);
        var layerGroup = L.layerGroup();
        var lastHeads;

        for ( var i=0; i<dates.length; i++ ){
            var heads = data[dates[i]];
            if (typeof heads == "string"){
                heads = $.parseJSON(heads)
            }

            var allValues = [];
            for (var j=0; j<heads[0].length; j++){
                allValues = $.merge(allValues, heads[0][j]);
            }

            var filteredValues = [];
            for (j=0; j<allValues.length; j++){
                var value = allValues[j];
                if (value != null){
                    filteredValues.push(value)
                }
            }

            lastHeads = heads;
        }

        // Calculating 5%/95% percentile
        filteredValues.sort(function(a,b){return a - b});
        //var min = filteredValues[Math.round(5 * filteredValues.length/100)];
        //var max = filteredValues[Math.round(95 * filteredValues.length/100)];

        var min = -26.6;
        var max = 0.93;

        layerGroup = this.createHeatMap(lastHeads[0], min, max, I.model.boundingBox, I.model.gridSize, layerGroup);
        return layerGroup;
    },
    _addHeadsLayer: function ( data, map ){

        // Data is a time-value object
        // where value is a three dimensional heads array
        var dates = Object.keys(data);
        var layerGroup = L.layerGroup();

        var info = L.control();
        info.onAdd = function (map) {
            this._div = L.DomUtil.create('div', 'info');
            this.update();
            return this._div;
        };
        info.update = function (head, min, max) {
            head = Math.round(head*100)/100;
            max = Math.round(max*100)/100;
            min = Math.round(min*100)/100;

            this._div.innerHTML = '' +
                '<h4>Groundwater Heads</h4>' +
                (
                    head ?
                    '<p>Head: <b>' + head + '</b> m</p>' +
                    'min: '+ min + ' m; ' +
                    'max: '+ max + ' m' :
                    'Click on the map'
                );
        };
        info.addTo(map);

        for ( var i=0; i<dates.length; i++ ){
            var heads = data[dates[i]];
            if (typeof heads == "string"){
                heads = $.parseJSON(heads)
            }

            var allHeads =[];
            for (var j=0; j<heads[0].length; j++){
                allHeads = $.merge(allHeads, heads[0][j]);
            }

            // Calculating 5%/95% percentile
            allHeads.sort();
            var min = allHeads[Math.round(5 * allHeads.length/100)];
            var max = allHeads[Math.round(95 * allHeads.length/100)];

            layerGroup = this.createHeadsLayer(heads, min, max, dates[i], this.boundingBox, this.gridSize, layerGroup, info, map);
        }

        var legend = L.control({position: 'bottomright'});
        legend.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'info legend'),
                grades = that.calculateLegend(min, max, 6),
                labels = [];

            // loop through our density intervals and generate a label with a colored square for each interval
            for (var i = 0; i < grades.length; i++) {
                div.innerHTML +=
                    '<i style="background: ' + that.getColor(min, max, grades[i]) + '"></i>' +  grades[i] + '<br>';
            }

            return div;
        };

        legend.addTo(map);

        var sliderControl = L.control.sliderControl({
            position: "topright",
            layer: layerGroup,
            sameDate: true,
            showDate: dates[0],
            showSlider: dates.length>1
        });

        map.addControl(sliderControl);
        sliderControl.startSlider();
    },
    calculateLegend: function (min, max, numberOfItems) {

        max = Math.ceil(max/10)*10;
        min = Math.floor(min/10)*10;

        var delta = max-min;
        var dn = delta/numberOfItems;

        var grades = [];
        for (var i=0; i<numberOfItems; i++){
            grades.push(Math.round(min+i*dn));
        }

        return grades;
    }
};

I.results = {
    baseModel: null,
    scenarios: null,
    initialized: false,
    activeMaps: [],
    headValues: {
        min: null,
        max: null,
        mean: null
    },
    initialize: function( baseModelId ) {
        if (this.baseModel == null) {
            $.when(
                $.getJSON( "/api/modflowmodels/"+baseModelId+".json", function ( data ) {
                    I.results.baseModel = data;
                    I.results.baseModel.show = true;
                }),

                $.getJSON( "/api/modflowmodels/"+baseModelId+"/scenarios.json", function ( data ) {
                    I.results.scenarios = data;
                    $.each(I.results.scenarios, function (key, scenario) {
                       scenario.show = false;
                    });
                    I.results.scenarios[I.results.scenarios.length-1].show = true;
                })
            ).then(function(){

                $.getJSON( "/api/modflowmodels/"+I.results.baseModel.id+"/heads.json", function ( heads ) {

                    if (! $.isArray(heads)) {
                        I.results.baseModel.heads = heads;
                        I.results.updateMinMaxHeadValues(heads.min, heads.max);
                        I.results.headValues.mean = heads.mean;

                        I.results.addMap( I.results.baseModel );
                        var bounds = [[I.model.boundingBox.y_min, I.model.boundingBox.x_min],[I.model.boundingBox.y_max, I.model.boundingBox.x_max]];
                        var url = '/api/modflowmodels/'+I.results.baseModel.id+'/heads/image.png?min='+((I.results.headValues.mean+I.results.headValues.min)/2)+'&max='+I.results.headValues.max;
                        L.imageOverlay(url , bounds, { opacity: 0.5, position: 'back' }).addTo(I.results.baseModel.map).bringToBack();
                    }
                });

                $.each(I.results.scenarios, function (key, value) {
                    $.getJSON( "/api/modflowmodels/"+value.id+"/heads.json", function ( heads ) {
                        if (! $.isArray(heads)) {
                            value.heads = heads;
                            I.results.updateMinMaxHeadValues(heads.min, heads.max);

                            if (value.show == true){
                                I.results.addMap( value );
                                var bounds = [[I.model.boundingBox.y_min, I.model.boundingBox.x_min],[I.model.boundingBox.y_max, I.model.boundingBox.x_max]];
                                var url = '/api/modflowmodels/'+value.id+'/heads/image.png?min='+((I.results.headValues.mean+I.results.headValues.min)/2)+'&max='+I.results.headValues.max;
                                L.imageOverlay(url, bounds, { opacity: 0.5, position: 'back' }).addTo(value.map).bringToBack();
                            }
                        }
                    })
                });

                I.results.renderModelResultsSideBar( '#results_scenario_sidebar' );
                this.initialized = true;
            });
        }
    },
    updateMinMaxHeadValues: function (min, max) {
        if (I.results.headValues.min == null || I.results.headValues.min > min){
            I.results.headValues.min = min;
        }

        if (I.results.headValues.max == null || I.results.headValues.max > min){
            I.results.headValues.max = max;
        }
    },
    createChart: function( row ){
        if (I.results.chart){
            I.results.chart.destroy();
        }

        var rowData = I.results.baseModel.heads.data[row];

        var leftValue = null;
        var rightValue = null;
        for (var i=0; i<rowData.length; i++){

            if (!rowData[i]){
                if (leftValue && !rightValue){
                    rightValue=i-1;
                }
            } else {
                if (!leftValue){
                    leftValue=i;
                }
            }
        }

        I.results.chart = c3.generate({
            bindto: '#results_chart',
            data: {
                columns: []
            },
            grid: {
                x: {
                    show: true,
                    lines: [
                        {value: leftValue, text: 'Eastern model border', position: 'middle'},
                        {value: rightValue, text: 'Western model border', position: 'middle'}
                    ]
                }
            }
        });

        var column = [I.results.baseModel.name];
        column = $.merge(column, I.results.baseModel.heads.data[row]);
        I.results.chart.load({columns: [column]});


        $.each(I.results.scenarios, function (key, value) {
            var column = [value.name];
            column = $.merge(column, value.heads.data[row]);
            I.results.chart.load({
                columns: [column]
            });
        });
    },
    renderModelResultsSideBar: function ( elementId ) {
        var html = '';
        html += '<ul class="list-group">';
        html += I.results.renderScenariosElement( I.results.baseModel );

        $.each(I.results.scenarios, function(key, model){
            html += I.results.renderScenariosElement( model );
        });

        html += '</ul>';
        $(elementId).html(html);
    },
    renderScenariosElement: function ( model ) {
        var html = '';
        html += '<li class="list-group-item">';
        html += '<img src="/api/modflowmodels/'+model.id+'/image.jpg" class="img-responsive" alt="'+model.name+'">';
        html += '<div class="scenario_selected_container">';

        if (model.show){
            html += '<div class="scenario_selected checked"></div>'
        } else (
            html += '<div class="scenario_selected"></div>'
        );

        html += '</div>';
        html += '</li>';

        return html;
    },
    addMap: function ( model ){
        var divName = "result_map_"+ model.id;
        var html = '';
        html += '<div class="col-sm-6 results_map_container">';
        html += '<div class="panel panel-default">';
        html += '<div class="panel-body">';
        html += '<div id="'+divName+'" class="results_map"></div>';
        html += model.name + '<br/>' + model.description;
        html += '</div>';
        html += '</div>';
        html += '</div>';
        $("#result_maps").append(html);

        model.map = L.map(divName, {zoomControl: false});
        model.map.on('moveend', function() {
            I.results.fitBounds(model.map.getBounds());
        });

        var area = L.geoJson($.parseJSON(I.model.data.area.geojson), I.model.styles.areaGeometry).addTo(model.map);
        model.map.fitBounds(area.getBounds());
        L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(model.map);

        area.on('click', function(e) {
            var row = I.results.calculateRowFromLatLng(e.latlng);
            I.results.createMarkerRow( row );
            I.results.createChart( row );
        });

        I.results.activeMaps.push(model.map);
    },
    fitBounds: function( bounds ){
        if (I.results.baseModel.map != null){
            I.results.baseModel.map.fitBounds( bounds );
        }

        $.each(I.results.scenarios, function (key, model) {
            if (model.map != null){
                model.map.fitBounds( bounds );
            }
        });
    },
    calculateRowFromLatLng: function(latLng){

        var gz = I.model.gridSize;
        var bb = I.model.boundingBox;

        var xMin = bb.x_min;
        var xMax = bb.x_max;
        var yMin = bb.y_min;
        var yMax = bb.y_max;
        var nCol = gz.n_x;
        var nRow = gz.n_y;
        var dX = (xMax-xMin)/nCol;
        var dY = (yMax-yMin)/nRow;

        var x = latLng.lng;
        var y = latLng.lat;

        var col = Math.floor((x-xMin)/dX);
        var row = Math.floor(nRow-((y-yMin)/dY));

        return row;
    },
    createMarkerRow: function (row) {

        $.each(I.results.activeMaps, function(key, map){
            map.eachLayer(function(layer){
                if (layer.marker){
                    map.removeLayer(layer);
                }
            });

            var yMin = I.model.boundingBox.y_min;
            var yMax = I.model.boundingBox.y_max;
            var nRow = I.model.gridSize.n_y;
            var dY = (yMax-yMin)/nRow;

            var bb = {};
            bb.x_min = I.model.boundingBox.x_min;
            bb.x_max = I.model.boundingBox.x_max;
            bb.y_min = I.model.boundingBox.y_max - row*dY-dY;
            bb.y_max = I.model.boundingBox.y_max - row*dY;

            var rectangle = I.model.createRectangle(
                bb,
                {color: "grey", weight: 0.5, fillColor: 'grey', fillOpacity: 0.5}
            );

            rectangle.marker = true;
            rectangle.addTo(map).bringToFront();
        });
    }
};