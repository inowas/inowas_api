var myGeoJSONPath = 'geojson/worldmap.geo.json';
var myCustomStyle = {
    stroke: false,
    fill: true,
    fillColor: '#c0c0c0',
    fillOpacity: 1
};

$.getJSON(myGeoJSONPath,function(data){
    var modelsMap = L.map('models-map').setView([40, 0], 0);

    L.geoJson(data, {
        clickable: false,
        style: myCustomStyle
    }).addTo(modelsMap);

    modelsMap.touchZoom.disable();
    modelsMap.doubleClickZoom.disable();
    modelsMap.scrollWheelZoom.disable();
    modelsMap.boxZoom.disable();
    modelsMap.keyboard.disable();
    modelsMap.dragging.disable();
    $("#models-map").find(".leaflet-control-container").css("visibility", "hidden");
});