// global namespace
var I = I || {};

I.user = {
    apiKey: '',
    setApiKey: function (apiKey) {
        this.apiKey = apiKey;
        $.ajaxSetup({
            headers : {'X-AUTH-TOKEN' : apiKey }
        });
    }
};
I.model = {
    id: null,
    activeCells: null,
    boundingBox: null,
    gridSize: null,
    activeCellsGridLayer: null,
    boundingBoxLayer: null,
    wellsLayer: null,
    data: {
        area: null,
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
            sniw:  {radius: 5, color: 'red', weight: 2, fillColor: 'darkgreen', fillOpacity: 0.7},
            puw: {radius: 5, color: 'black', weight: 1, fillColor: 'darkblue', fillOpacity: 0.7},
            snpw:  {radius: 5, color: 'red', weight: 2, fillColor: 'darkblue', fillOpacity: 0.7},
            prw: {radius: 5, color: 'black', weight: 1, fillColor: 'darkblue', fillOpacity: 0.7},
            smw: {radius: 5, color: 'black', weight: 1, fillColor: 'red', fillOpacity: 1},
            snw: {radius: 5, color: 'black', weight: 1, fillColor: 'yellow', fillOpacity: 1},
            snifw:  {radius: 5, color: '#63b3ea', weight: 2, fillColor: '#bbdff6', fillOpacity: 0.7},
        },
        river: {color: "#000", weight: 0.5, fillColor: "blue", fillOpacity: 0}
    },
    initialize: function(id){
        this.id = id;
        that = this;

        $.getJSON( "/api/modflowmodels/"+id+"/properties.json", function ( data ) {
            that.activeCells = data.active_cells;
            that.boundingBox = data.bounding_box;
            that.gridSize = data.grid_size;
            that.loadSummary();
        });

        $.getJSON( "/api/modflowmodels/"+this.id+"/area.json", function ( data ) {
            that.data.area = data;
        });

        $.getJSON( "/api/modflowmodels/"+this.id+"/wells.json", function ( data ) {
            that.data.wel = data;
        });

        $.getJSON( "/api/modflowmodels/"+this.id+"/rivers.json", function ( data ) {
            that.data.riv = data;
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
                                $.getJSON("/api/modflowmodels/" + that.id + "/wells.json", function (data) {
                                    that.data.wel = data;
                                    that.updateWells(true);
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

            $.getJSON( "/api/modflowmodels/"+I.model.id+"/contents/summary.json", function ( data ) {
                that.content.summary =  data.html;
                that.area.polygonJSON = data.geojson;
                that.createAreaLayer().addTo(map);
                map.fitBounds(that.createBoundingBoxPolygon(that.boundingBox).getBounds());
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

            var map = this.createBaseMap( 'map-area' );
            var boundingBox = this.createBoundingBoxLayer(this.boundingBox).addTo(map);
            var areaPolygon = L.geoJson($.parseJSON(this.data.area.geojson), this.styles.areaGeometry).addTo(map);
            var areaActiveCells = this.createAreaActiveCellsLayer(this.activeCells, this.boundingBox, this.gridSize, this.data.area.mutable);
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
    loadBoundaries: function(refresh) {
        if (this.maps.boundaries == null || refresh == true) {
            if (refresh == true && this.maps.boundaries != null){
                this.maps.boundaries.remove();
            }

            var map = this.createBaseMap( 'boundaries-map' );

            var boundingBox = this.createBoundingBoxLayer(this.boundingBox).addTo(map);
            var areaPolygon = L.geoJson(jQuery.parseJSON(this.area.polygonJSON), this.styles.areaGeometry).addTo(map);
            map.fitBounds(this.createBoundingBoxPolygon(this.boundingBox).getBounds());
            var wells = this.createWellsLayer(this.data.wel).addTo(map);
            var rivers = this.createRiversLayer(this.data.riv).addTo(map);

            function onMapClick(e) {
                console.log("You clicked the map at " + e.latlng);
            }
            areaPolygon.on('click', onMapClick);

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
            var areaPolygon = L.geoJson(jQuery.parseJSON(this.area.polygonJSON), this.styles.areaGeometry).addTo(map);
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
            var areaPolygon = L.geoJson(jQuery.parseJSON(this.area.polygonJSON), this.styles.areaGeometry).addTo(map);
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
            L.geoJson(jQuery.parseJSON(this.area.polygonJSON), this.styles.areaGeometry).addTo( map );
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
        return L.geoJson(jQuery.parseJSON(this.area.polygonJSON), this.styles.areaGeometry);
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
                if (activeCells.cells[row] == undefined || activeCells.cells[row][col] == undefined){
                    value = false;
                }

                var rectangle = this.createRectangle(bb, this.getStyle('area', value));
                rectangle.col = col;
                rectangle.row = row;

                if (mutable){
                    rectangle.on('click', function(e) {

                        if (activeCells.cells[e.target.row] == undefined || activeCells.cells[e.target.row][e.target.col] == undefined){
                            activeCells.cells[e.target.row][e.target.col] = true;
                            e.target.setStyle(that.getStyle('area', activeCells.cells[e.target.row][e.target.col]));
                        } else {
                            activeCells.cells[e.target.row][e.target.col] = undefined;
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
                var well = L.circleMarker(L.latLng(item.point.y, item.point.x), I.model.styles.wells[key]).bindPopup(item.name).addTo(layer);
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

                        var rectangle = that.createRectangle(bb, that.getStyle('wells', true));
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
                layer.raw = raw;
                layer.addTo(layers)
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

                    var rectangle = that.createRectangle(bb, that.getStyle('wells', true));
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
    createHeadsLayer: function (heads, min, max, time, boundingBox, gridSize, layerGroup, info) {

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

                var rectangle = this.createRectangle(
                    bb,
                    {color: "blue", weight: 0, fillColor: this.getColor(min, max, value), fillOpacity: 0.2, time: time}
                );

                rectangle.time = time;
                rectangle.col = col;
                rectangle.row = row;
                rectangle.lay = lay;
                rectangle.value = value;
                rectangle.on('click', function(e) {

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
                L.circleMarker([item.point.y, item.point.x], I.model.styles.wells[key]).bindPopup("Well "+item.name).addTo(geographyLayer);

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

            layerGroup = this.createHeadsLayer(heads, min, max, dates[i], this.boundingBox, this.gridSize, layerGroup, info);
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