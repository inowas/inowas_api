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
    boundaries: {},
    content: {},
    maps: {},
    styles: {
        inactive: {color: "#000", weight: 0, fillColor: "#000", fillOpacity: 0.7},
        active: {color: "#ff7800", weight: 0, fillColor: "#000", fillOpacity: 0},
        boundingBox: {color: "#000", weight: 0.5, fillColor: "blue", fillOpacity: 0},
        areaGeometry: {color: "#000", weight: 0.5, fillColor: "blue", fillOpacity: 0.1},
        wells : {
            cw: {color: 'black', weight: 1, fillColor: 'darkgreen', fillOpacity: 0.7},
            iw: {color: 'black', weight: 1, fillColor: 'darkblue', fillOpacity: 0.7},
            pw: {color: 'black', weight: 1, fillColor: 'darkgreen', fillOpacity: 0.7},
            smw: {color: 'black', weight: 1, fillColor: 'red', fillOpacity: 1},
            snw: {color: 'black', weight: 1, fillColor: 'yellow', fillOpacity: 1}
        }
    },
    getStyle: function (type, value){
        if (type == 'area'){
            if (value == true){
                return this.styles.active;
            } else if (value == false) {
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

            var map = this.maps.summary = this.createBaseMap('map-summary', { zoomControl:false });
            map.touchZoom.disable();
            map.doubleClickZoom.disable();
            map.scrollWheelZoom.disable();
            map.boxZoom.disable();
            map.keyboard.disable();
            map.dragging.disable();

            $.getJSON( "/api/modflowmodels/"+I.model.id+"/contents/summary.json", function ( data ) {
                prop.content.summary =  data.html;
                prop.area.polygonJSON = data.geojson;
                L.geoJson(jQuery.parseJSON(prop.area.polygonJSON), prop.styles.areaGeometry).addTo(map);
                map.fitBounds(prop.createBoundingBoxPolygon(prop.boundingBox).getBounds());
                $(".content_summary").html( prop.content.summary );
            });

            $.getJSON( "/api/modflowmodels/"+I.model.id+"/wells.json?srid=4326", function ( data ) {
                prop.boundaries.wells = data;
                prop.createWellsLayer( data ).addTo(map);
            });
        }
    },

    loadArea: function ( refresh ) {

        if (this.maps.area == null || refresh == true) {
            if (refresh == true){
                this.maps.area.remove();
            }

            var map = this.createBaseMap( 'map-area' );
            var boundingBox = this.createBoundingBoxLayer(prop.boundingBox).addTo(map);
            var polygon = L.geoJson(jQuery.parseJSON(this.area.polygonJSON), prop.styles.areaGeometry);
            var activeCells = this.createActiveCellsGridLayer(prop.activeCells, prop.boundingBox, prop.gridSize).addTo(map);
            map.fitBounds(prop.createBoundingBoxPolygon(prop.boundingBox).getBounds());

            var baseMaps = {};
            var overlayMaps = {"Area": polygon, "Bounding Box": boundingBox, "Active Cells": activeCells};
            L.control.layers(baseMaps, overlayMaps).addTo(map);
        }
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
    createWellsLayer: function( wells ) {
        var layer = new L.LayerGroup();
        for (var key in wells) {
            if (!wells.hasOwnProperty(key)) continue;

            var items = wells[key];
            items.forEach(function (item) {
                L.circle([item.point.y, item.point.x], 100, I.model.styles.wells[key]).bindPopup("Well "+item.name).addTo(layer);
            });
        }

        this.wellsLayer = layer;
        return layer;
    },
    createActiveCellsGridLayer: function (activeCells, boundingBox, gridSize) {

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

                var rectangle = this.createRectangle(bb, this.getStyle('area', activeCells.cells[row][col]));
                rectangle.col = col;
                rectangle.row = row;
                rectangle.on('click', function(e) {
                    activeCells.cells[e.target.row][e.target.col] = !activeCells.cells[e.target.row][e.target.col];
                    e.target.setStyle(prop.getStyle('area', activeCells.cells[e.target.row][e.target.col]));
                    $('#btn_save_area').show();
                });
                rectangle.addTo(layers);
            }
        }

        return layers;
    }
};
