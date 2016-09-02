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
            puw: {radius: 5, color: 'black', weight: 1, fillColor: 'darkblue', fillOpacity: 0.7},
            prw: {radius: 5, color: 'black', weight: 1, fillColor: 'darkblue', fillOpacity: 0.7},
            smw: {radius: 5, color: 'black', weight: 1, fillColor: 'red', fillOpacity: 1},
            snw: {radius: 5, color: 'black', weight: 1, fillColor: 'yellow', fillOpacity: 1}
        },
        river: {color: "#000", weight: 0.5, fillColor: "blue", fillOpacity: 0}
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
    loadProperties: function (id) {
        prop = this;
        this.id = id;

        $.getJSON( "/api/modflowmodels/"+id+"/properties.json", function ( data ) {
            prop.activeCells = data.active_cells;
            prop.boundingBox = data.bounding_box;
            prop.gridSize = data.grid_size;
        });

        return true;
    },
    updateProperties: function (id) {
        prop = this;

        $.ajax({
            type: 'PUT',
            url: '/api/modflowmodels/'+id+'.json',
            data: { 'active_cells' : JSON.stringify(I.model.activeCells.cells) },
            statusCode: {
                200: function( data ) {
                    prop.activeCells = data.active_cells;
                    prop.boundingBox = data.bounding_box;
                    prop.gridSize = data.grid_size;
                    prop.buttons.updateActiveCells.disable();
                }
            }
        });

        return true;
    },
    loadSummary: function ( refresh ) {
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
                prop.content.summary =  data.html;
                prop.area.polygonJSON = data.geojson;
                prop.createAreaLayer().addTo(map);
                map.fitBounds(prop.createBoundingBoxPolygon(prop.boundingBox).getBounds());
                $(".content_summary").html( prop.content.summary );
            });

            this._loadAndAddWells( map, false );
        }
    },
    loadArea: function ( refresh ) {
        if (this.maps.area == null || refresh == true) {
            if (refresh == true){
                this.maps.area.remove();
            }

            var map = this.createBaseMap( 'map-area' );
            var boundingBox = this.createBoundingBoxLayer(prop.boundingBox).addTo(map);
            var polygon = L.geoJson(jQuery.parseJSON(this.area.polygonJSON), prop.styles.areaGeometry).addTo(map);
            var activeCells = this.createActiveCellsGridLayer(prop.activeCells, prop.boundingBox, prop.gridSize);
            map.fitBounds(prop.createBoundingBoxPolygon(prop.boundingBox).getBounds());

            var baseMaps = {};
            var overlayMaps = {"Area": polygon, "Bounding Box": boundingBox, "Active Cells": activeCells};
            L.control.layers(baseMaps, overlayMaps).addTo(map);

            this.buttons.updateActiveCells = L.easyButton('fa-save', function(btn, map){
                I.model.updateProperties( I.model.id );
            }).disable().addTo(map);

            this.maps.area = map;
        }
    },
    loadBoundaries: function (refresh ) {
        if (this.maps.boundaries == null || refresh == true) {
            if (refresh == true && this.maps.boundaries != null){
                this.maps.boundaries.remove();
            }

            var map = this.createBaseMap( 'boundaries-map' );
            L.geoJson(jQuery.parseJSON(this.area.polygonJSON), prop.styles.areaGeometry).addTo( map );
            map.fitBounds(prop.createBoundingBoxPolygon(prop.boundingBox).getBounds());

            this._loadAndAddWells( map, false );
            this._loadAndAddRivers( map, false );
            this.maps.boundaries = map;
        }
    },
    loadWells: function (refresh ) {
        if (this.maps.wel == null || refresh == true) {
            if (refresh == true && this.maps.wel != null){
                this.maps.wel.remove();
            }

            var map = this.createBaseMap( 'wells-map' );
            L.geoJson(jQuery.parseJSON(this.area.polygonJSON), prop.styles.areaGeometry).addTo( map );
            map.fitBounds(prop.createBoundingBoxPolygon(prop.boundingBox).getBounds());

            this._loadAndAddWells( map, true );
            this.maps.wel = map;
        }
    },
    loadRivers: function (refresh ) {
        if (this.maps.riv == null || refresh == true) {
            if (refresh == true && this.maps.riv != null){
                this.maps.riv.remove();
            }

            var map = this.createBaseMap( 'rivers-map' );
            L.geoJson(jQuery.parseJSON(this.area.polygonJSON), prop.styles.areaGeometry).addTo( map );
            map.fitBounds(prop.createBoundingBoxPolygon(prop.boundingBox).getBounds());

            this._loadAndAddRivers( map, true );

            this.maps.riv = map;
        }
    },
    loadHeads: function (refresh) {
        if (this.maps.heads == null || refresh == true) {
            if (refresh == true && this.maps.heads != null){
                this.maps.heads.remove();
            }

            var map = this.createBaseMap( 'heads-map' );
            L.geoJson(jQuery.parseJSON(this.area.polygonJSON), prop.styles.areaGeometry).addTo( map );
            map.fitBounds(prop.createBoundingBoxPolygon(prop.boundingBox).getBounds());
            this._loadAndAddHeads( map );
        }
    },
    createAreaLayer: function() {
        return L.geoJson(jQuery.parseJSON(this.area.polygonJSON), this.styles.areaGeometry);
    },
    createBaseMap: function( id, options ) {
        var map = new L.map( id, options );
        L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {
            maxZoom: 18,
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
            '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
            'Imagery © <a href="http://mapbox.com">Mapbox</a>',
            id: 'mapbox.streets'
        }).addTo(map);
        map.addControl( new L.Control.FullScreen({
            position: 'bottomright',
            forceSeparateButton: true
        }));
        return map;
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
    createActiveCellsGridLayer: function (activeCells, boundingBox, gridSize) {

        that = this;
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
                rectangle.on('click', function(e) {

                    if (activeCells.cells[e.target.row] == undefined || activeCells.cells[e.target.row][e.target.col] == undefined){
                        activeCells.cells[e.target.row][e.target.col] = true;
                        e.target.setStyle(prop.getStyle('area', activeCells.cells[e.target.row][e.target.col]));
                    } else {
                        activeCells.cells[e.target.row][e.target.col] = undefined;
                        e.target.setStyle(prop.getStyle('area', false));
                    }

                    that.buttons.updateActiveCells.enable();
                });
                rectangle.addTo(layers);
            }
        }

        return layers;
    },
    createHeadsLayer: function (heads, min, max, time, boundingBox, gridSize, layerGroup) {

        var lay = 0;
        heads = heads[lay];

        var heatmap = new Rainbow();
        heatmap.setSpectrum('red', 'yellow', 'lime', 'aqua', 'blue');
        heatmap.setNumberRange(min, max);

        that = this;
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
                }

                var rectangle = this.createRectangle(
                    bb,
                    {color: "blue", weight: 0, fillColor: '#'+heatmap.colorAt(value), fillOpacity: 0.2, time: time}
                );

                rectangle.time = time;
                rectangle.col = col;
                rectangle.row = row;
                rectangle.lay = lay;
                rectangle.on('click', function(e) {

                    var allHeads = $.extend({}, that.heads);
                    var keys = Object.keys(allHeads);

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
                });

                rectangle.bindPopup("Groundwater Head: "+value);
                rectangle.addTo(layerGroup);
            }
        }

        return layerGroup;
    },
    createWellCellsLayer: function (activeCells, boundingBox, gridSize) {

        that = this;
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

                var rectangle = this.createRectangle(bb, this.getStyle('wells', value));
                rectangle.addTo(layers);
            }
        }

        return layers;
    },
    _loadAndAddWells: function( map, addActiveCells ){
        var that = this;
        if (this.boundaries.wel !== null) {
            this._addWellsLayer( this.boundaries.wel, map, addActiveCells );
        } else {
            $.getJSON( "/api/modflowmodels/"+this.id+"/wells.json?srid=4326", function ( data ) {
                that.boundaries.wel = data;
                that._addWellsLayer( data, map, addActiveCells );
            });
        }
    },
    _loadAndAddRivers: function( map, addActiveCells ){
        var that = this;
        if (this.boundaries.riv !== null) {
            this._addRiversLayer( this.boundaries.riv, map, addActiveCells );
        } else {
            $.getJSON( "/api/modflowmodels/"+this.id+"/rivers.json?srid=4326", function ( data ) {
                that.boundaries.riv = data;
                that._addRiversLayer( data, map, addActiveCells );
            });
        }
    },
    _loadAndAddHeads: function( map ){
        var that = this;
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
            var activeCellsLayer = this.createWellCellsLayer(active_cells, this.boundingBox, this.gridSize);
            activeCellsLayer.addTo(map);
            var baseMaps = {};
            var overlayMaps = {"Wells": geographyLayer, "Active Cells": activeCellsLayer};
            L.control.layers(baseMaps, overlayMaps).addTo(map);
        }
    },
    _addRiversLayer: function ( rivers, map , addActiveCells){

        var geographyLayer = new L.LayerGroup();

        var active_cells = {};
        active_cells.cells = [];

        for (var rivKey in rivers){
            if (!rivers.hasOwnProperty(rivKey)) continue;
            var line = rivers[rivKey]['line'];

            var linePoints = [];

            for (var pointKey in line) {
                if (! line.hasOwnProperty(pointKey)) continue;
                if (isNaN(parseInt(pointKey))) continue;

                linePoints.push(L.latLng(line[pointKey][1], line[pointKey][0]));
            }

            L.polyline(linePoints, this.styles).addTo(geographyLayer);

            if (addActiveCells == true){
                for(var rowProperty in rivers[rivKey].active_cells.cells) {
                    if (!rivers[rivKey].active_cells.cells.hasOwnProperty(rowProperty)){continue;}

                    if (active_cells.cells[rowProperty] == null) {
                        active_cells.cells[rowProperty] = [];
                    }

                    var row = rivers[rivKey].active_cells.cells[rowProperty];

                    for(var colProperty in row) {
                        if (!row.hasOwnProperty(colProperty)){continue;}
                        active_cells.cells[rowProperty][colProperty] = row[colProperty];
                    }
                }
            }
        }

        geographyLayer.addTo(map);

        if (addActiveCells == true) {
            var activeCellsLayer = this.createWellCellsLayer(active_cells, this.boundingBox, this.gridSize).addTo(map);
            var baseMaps = {};
            var overlayMaps = {"River": geographyLayer, "Active Cells": activeCellsLayer};
            L.control.layers(baseMaps, overlayMaps).addTo(map);
        }
    },
    _addHeadsLayer: function ( data, map ){

        // Data is a time-value object
        // where value is a three dimensional heads array
        var dates = Object.keys(data);

        var layerGroup = L.layerGroup();
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

            layerGroup = this.createHeadsLayer(heads, min, max, dates[i], this.boundingBox, this.gridSize, layerGroup);
        }

        var sliderControl = L.control.sliderControl({
            position: "topright",
            layer: layerGroup,
            sameDate: true,
            showDate: dates[0],
            showSlider: dates.length>1
        });

        map.addControl(sliderControl);
        sliderControl.startSlider();
    }
};