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
    boundingBoxLayer: null,
    activeCellsGridLayer: null,
    area: null,
    map: null,
    styles: {
        inactive:{color: "#000", weight: 0, fillColor: "#000", fillOpacity: 0.7},
        active:{color: "#ff7800", weight: 0, fillColor: "#000", fillOpacity: 0},
        boundingBox:{color: "#000", weight: 0.5, fillColor: "blue", fillOpacity: 0.1}
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
    loadPropertiesById: function (id) {
        this.id = id;
        prop = this;
        $.getJSON( "/api/modflowmodels/"+id+"/properties.json", function ( data ) {
            prop.activeCells = data.active_cells;
            prop.boundingBox = data.bounding_box;
            prop.gridSize = data.grid_size;
            prop.createMap();
            prop.createBoundingBoxLayer(prop.boundingBox).addTo(prop.map);
            prop.map.fitBounds(prop.createBoundingBoxPolygon(prop.boundingBox).getBounds());
            prop.createActiveCellsGridLayer(prop.activeCells, prop.boundingBox, prop.gridSize).addTo(prop.map);
        });
    },
    createMap: function() {
        this.map = L.map('map').setView([
                (this.boundingBox.x_min + this.boundingBox.x_max)/2,
                (this.boundingBox.y_min + this.boundingBox.y_max)/2
            ], 5
        );

        L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {
            maxZoom: 18,
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
            '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
            'Imagery © <a href="http://mapbox.com">Mapbox</a>',
            id: 'mapbox.streets'
        }).addTo(this.map);
    },
    createBoundingBoxLayer: function(boundingBox) {
        var layer = new L.LayerGroup();
        this.createBoundingBoxPolygon(boundingBox).addTo(layer);
        this.boundingBoxLayer = layer;
        return layer;
    },
    createBoundingBoxPolygon: function(boundingBox) {
        return this.createRectangle(boundingBox, this.styles.boundingBox);
    },
    createRectangle: function(boundingBox, style){
        return new L.Rectangle([[boundingBox.y_min, boundingBox.x_min], [boundingBox.y_max, boundingBox.x_max]], style);
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
                });
                rectangle.addTo(layers);
            }
        }

        return layers;
    }
};
