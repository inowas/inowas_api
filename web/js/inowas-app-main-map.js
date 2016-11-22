var map = L.map('map', {
    zoomControl: false
}).setView([50.9661, 13.92367], 15);

L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
    subdomains: 'abcd',
    maxZoom: 19
}).addTo(map);

$(window).on("resize", function() {
    $("#map").height($(window).height()).width($(window).width());
    map.invalidateSize();
}).trigger("resize");

var info = L.control();
info.onAdd = function (map) {
    this._div = L.DomUtil.create('div', 'layers'); // create a div with a class "info"
    this.update();
    return this._div;
};

// method that we will use to update the control based on feature properties passed
info.update = function (props) {
    this._div.innerHTML = '' +
        '<div class="panel panel-primary"> ' +
        '   <div class="panel-heading"> ' +
        '       <h3 class="panel-title">Panel title</h3> ' +
        '   </div> ' +
        '   <div class="panel-body"> Panel content<br>Panel content<br> </div> ' +
        '</div>';
};

info.addTo(map);

L.control.zoom({
    position:'topright'
}).addTo(map);


L.easyButton({
    position: 'topright',
    states:[{
        stateName: 'get-center',
        onClick: function(button, map){
            map.setView([50.9661, 13.92367], 16);
        },
        title: 'Zoom to the model boundaries.',
        icon: 'fa-crosshairs fa-lg'
    }]
}).addTo( map );


function getColor(d) {
    return  d > 1000 ? '#800026' :
            d > 500  ? '#BD0026' :
            d > 200  ? '#E31A1C' :
            d > 100  ? '#FC4E2A' :
            d > 50   ? '#FD8D3C' :
            d > 20   ? '#FEB24C' :
            d > 10   ? '#FED976' :
                       '#FFEDA0';
}

var legend = L.control({position: 'bottomright'});
legend.onAdd = function (map) {

    var div = L.DomUtil.create('div', 'info legend'),
        grades = [0, 10, 20, 50, 100, 200, 500, 1000];

    div.innerHTML += '<i>m</i><br>';
    for (var i = 0; i < grades.length; i++) {
        div.innerHTML += '<i style="background:' + getColor(grades[i] + 1) + '">'+grades[i]+'</i><br>';
    }

    return div;
};
legend.addTo(map);


// Create additional Control placeholders
function addControlPlaceholders(map) {
    var corners = map._controlCorners,
        l = 'leaflet-',
        container = map._controlContainer;

    function createCorner(vSide, hSide) {
        var className = l + vSide + ' ' + l + hSide;

        corners[vSide + hSide] = L.DomUtil.create('div', className, container);
    }

    createCorner('verticalcenter', 'left');
    createCorner('verticalcenter', 'right');

}
addControlPlaceholders(map);

