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
    content: {},
    maps: {
        area: null,
        boundaries: null,
        riv: null,
        chb: null,
        ghb: null,
        rch: null,
        wel: null,
        summary: null
    },
    buttons: {
        updateActiveCells: null
    },
    styles: {
        inactive: {color: "#000", weight: 0, fillColor: "#000", fillOpacity: 0.7},
        active: {color: "#ff7800", weight: 0, fillColor: "#000", fillOpacity: 0},
        boundingBox: {color: "#000", weight: 0.5, fillColor: "blue", fillOpacity: 0},
        areaGeometry: {color: "#000", weight: 0.5, fillColor: "blue", fillOpacity: 0.1},
        wells : {
            cw: {radius: 3, color: 'black', weight: 1, fillColor: 'darkgreen', fillOpacity: 0.7},
            iw: {radius: 3, color: 'black', weight: 1, fillColor: 'darkblue', fillOpacity: 0.7},
            pw: {radius: 3, color: 'black', weight: 1, fillColor: 'darkgreen', fillOpacity: 0.7},
            smw: {radius: 3, color: 'black', weight: 1, fillColor: 'red', fillOpacity: 1},
            snw: {radius: 3, color: 'black', weight: 1, fillColor: 'yellow', fillOpacity: 1}
        }
    },
    getStyle: function (type, value){
        if (type == 'area'){
            if (value == true){
                return this.styles.active;
            } else {
                return this.styles.inactive;
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

            this._loadAndAddWells( map );
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
            this._loadAndAddWells( map );
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
            this._loadAndAddWells( map );
            this.maps.wel = map;
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
    _loadAndAddWells: function( map ){
        var that = this;
        if (this.boundaries.wel !== null) {
            this._addWellsLayer( this.boundaries.wel, map );
        } else {
            $.getJSON( "/api/modflowmodels/"+this.id+"/wells.json?srid=4326", function ( data ) {
                that.boundaries.wel = data;
                that._addWellsLayer( data, map );
            });
        }
    },
    _addWellsLayer: function ( wells, map ){
        var layer = new L.LayerGroup();
        for (var key in wells) {
            if (!wells.hasOwnProperty(key)) continue;

            var items = wells[key];
            items.forEach(function (item) {
                L.circleMarker([item.point.y, item.point.x], I.model.styles.wells[key]).bindPopup("Well "+item.name).addTo(layer);
            });
        }
        return layer.addTo(map);
    }
};
