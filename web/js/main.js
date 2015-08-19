// ========= global variables ========================================
var featureXY;// XY of edited features, set globaly to be accessed from different divs than map
var map;
// ========= map section =============================================
function init() {
	
	map = new ol.Map({
		target:'map',
		renderer:'canvas',
		view: new ol.View({	
		projection: 'EPSG:3857',
		center:[1120000,6500000],
		zoom:7	
		})
	});

	var baseLayer = new ol.layer.Tile({
		source: new ol.source.OSM()
	});
	map.addLayer(baseLayer);
/**=====================================Add WMS=============================
adding of raster WMS layer
*/	
	/**var wmsRaster = new ol.layer.Image({
		source: new ol.source.ImageWMS({
		url: 'http://localhost/cgi-bin/mapserv.exe?map=c:\\osgeo4w\\Apache\\htdocs\\mapfile.map&',
		params: {LAYERS:'mylayer'},
		serverType: 'mapserver'
		})
	});
	wmsRaster.setOpacity(0.75);
	map.addLayer(wmsRaster);*/
/**====================================Add WFS==============================
connection and adding of bounding box and wells layers
*/
	sourcePolygon = new ol.source.Vector({
	loader: function(extent) {
		$.ajax('http://localhost/cgi-bin/tinyows.exe',{
			type: 'GET',
			data: {
				service: 'WFS',
				version: '1.1.0',
				request: 'GetFeature',
				typename: 'tows:bounding_box',
				srsname: 'EPSG:3857',
				outputFormat: 'application/json',
				bbox: extent.join(',') + ',EPSG:3857'
				},
			}).done(loadFeaturesP);
		},
		strategy: ol.loadingstrategy.tile(new ol.tilegrid.createXYZ({
			maxZoom: 19
			})),
	});
	sourcePoint = new ol.source.Vector({
	loader: function(extent) {
		$.ajax('http://localhost/cgi-bin/tinyows.exe',{
			type: 'GET',
			data: {
				service: 'WFS',
				version: '1.1.0',
				request: 'GetFeature',
				typename: 'tows:wellinfo',
				srsname: 'EPSG:3857',
				outputFormat: 'application/json',
				bbox: extent.join(',') + ',EPSG:3857'
				},
			}).done(loadFeatures);
		},
		strategy: ol.loadingstrategy.tile(new ol.tilegrid.createXYZ({
			maxZoom: 19
			})),
	});

	window.loadFeaturesP = function(response) {
    	geoJSON = new ol.format.GeoJSON();
    	sourcePolygon.addFeatures(geoJSON.readFeatures(response));
	};

	layerPolygon = new ol.layer.Vector({
    	source: sourcePolygon,
    	style: new ol.style.Style({
			fill: new ol.style.Fill({
      			color: 'rgba(255, 255, 255, 0.2)'
    		}),
        	stroke: new ol.style.Stroke({
            	color: 'rgba(0, 0, 255, 1.0)',
            	width: 2
            	})
        	})
    	});
	map.addLayer(layerPolygon);
	
	window.loadFeatures = function(response) {
    	geoJSON = new ol.format.GeoJSON();
    	sourcePoint.addFeatures(geoJSON.readFeatures(response));
    };
	layerPoint = new ol.layer.Vector({
    	source: sourcePoint,
    	style: new ol.style.Style({
			image: new ol.style.Circle({
        		radius: 7,
        		fill: new ol.style.Fill({
          		color: [0, 153, 255, 1]
        		}),
        		stroke: new ol.style.Stroke({
					color: [255, 255, 255, 0.75],
          			width: 1.5
        		})
      		}),
      		zIndex: 100000
       	})
    });
	map.addLayer(layerPoint);
//==========================================Add interactions================	
	var select = new ol.interaction.Select();
    var modify = new ol.interaction.Modify({
		features: select.getFeatures()
	});
	map.addInteraction(select);
	map.addInteraction(modify);
/**===========================================Transaction Draw==============
Allows for drawing bounding box and wells
*/ 	
	var draw; //interaction is declared outside the function so that it can be removed to be changed
	var typeSelect = document.getElementById('insertType');//selected geometry type
	function drawing(){
		var value = typeSelect.value;
		var fType
		if (value == 'Point'){
			fType = "tows:wellinfo"
		}
		else if (value == 'Polygon'){
			fType = "tows:bounding_box"
		};
		if (value=='Point'){
			draw = new ol.interaction.Draw({
			source: sourcePoint,
			type: (value)
			});
			map.addInteraction(draw);
		}
		else if (value=='Polygon'){
			draw = new ol.interaction.Draw({
			source: sourcePolygon,
			type: (value)
			});
			map.addInteraction(draw);
		};
		draw.on('drawend', function(evt) {
			var format = new ol.format.WFS;
			var feature = evt.feature;
			var fid = feature.getId();
			var serializer = new XMLSerializer();
			var properties = feature.getProperties();
			var clone = new ol.Feature(properties);
    		var node = format.writeTransaction([clone],null, null, {
      			gmlOptions: {srsName: "EPSG:3857"},
      			featureNS: "http://localhost",
      			featureType: fType
			});
			
			$.ajax({
      			type: "POST",
      			url: "http://localhost/cgi-bin/tinyows.exe?",
				dataType: "xml",
      			data: serializer.serializeToString(node),
      			contentType: 'text/xml',
      			success: function(data) {
        			var result = format.readTransactionResponse(data);
        			//if (result && result.TransactionSummary.totalUpdated === 1) {
          			//		delete this.dirty_[fid];
					//		console.log(result);
					//};
        			console.log(result.insertIds[0]); 
					
			},
				context: this
    			});
  			
		});	
	};
	typeSelect.onchange = function(e) {
  		map.removeInteraction(draw);
		drawing();
	};
	
	/**var selected = select.getFeatures();
	var dirty = {};
	selected.on('add', function(evt) {
		var feature = evt.element;
		var fid = feature.getId();
		feature.on('change', function(evt) {
        	dirty[evt.target.getId()] = true;
        });
      });
	selected.on('remove', function(evt) {
       	var format = new ol.format.WFS;
		var feature = evt.element;
		var fid = feature.getId();
		var serializer = new XMLSerializer();
		if (dirty[fid]) {
			var properties = feature.getProperties();
    		// get rid of bbox which is not a real property
    		delete properties.bbox;
			delete properties.id;
    		var clone = new ol.Feature(properties);
    		//clone.setId(fid);
    		var node = format.writeTransaction(null,[clone], null, {
      			gmlOptions: {srsName: "EPSG:4326"},
      			featureNS: "http://localhost",
      			featureType: "tows:bounding_box"
			});
			console.log(serializer.serializeToString(node));
			$.ajax({
      			type: "POST",
      			url: "http://localhost/cgi-bin/tinyows.exe?",
				dataType: "xml",
      			data: serializer.serializeToString(node),
      			contentType: 'text/xml',
      			success: function(data) {
        			var result = format.readTransactionResponse(data);
        			if (result && result.TransactionSummary.totalUpdated === 1) {
          				delete this.dirty_[fid];
					console.log(result);};
        			        
      },
      context: this
    		});
  		}	
	});*/
  
	// ========= popup section =============================================
	map.on('dblclick', function(evt) {
  		var feature = map.forEachFeatureAtPixel(
			evt.pixel, 
			function(feature, layer) 
			{
				return feature
			});

		if (feature) {
			var geometry = feature.getGeometry();
			var coordinates  = geometry.getCoordinates();
			if (feature.type = 'Point'){
				featureXY = coordinates
				}
			else {
				featureXY = coordinates[0][0]
				};
			overlay.setPosition(featureXY);
			console.log(feature);
		}
	});
	
	var container = document.getElementById('popup');
	var closer = document.getElementById('popup-closer');
	/**
 	* Add a click handler to hide the popup.
 	* @return {boolean} Don't follow the href.
 	*/
	closer.onclick = function() {
  	overlay.setPosition();
  	closer.blur();
  	return false;
	};
	/**
 	* Create an overlay to anchor the popup to the map.
 	*/
	var overlay = new ol.Overlay(/** @type {ol.OverlayOptions} */ ({
  		element: container,
  		autoPan: true,
  		autoPanAnimation: {
    	duration: 250
  		}
	}));
	map.addOverlay(overlay);
};
// ========= form input section =============================================

function submitQuery(){
	//get the form data
	var formdata = $("form").serializeArray();

	//add to data request object
	var data = {};
	formdata.forEach(function(dataobj){
		data[dataobj.name] = dataobj.value;
		
	});
	//console.log (data);
	//call the php script
	$.ajax("php/getData.php", {
		data: data
	});
}

function submitWellInfo(){
	//get the form data
	var formdata = $("form").serializeArray();

	//add to data request object
	var data = {};
	formdata.forEach(function(dataobj){
		data[dataobj.name] = dataobj.value;
	});
	data['coordX'] = window.featureXY[0];
	data['coordY'] = window.featureXY[1];	
	console.log(data);
	//call the php script
	$.ajax("php/submitWellInfo.php", {
		data: data
	});
}
function removeMarker() {
	$.ajax("php/removeAll.php", {
		});
	sourcePolygon.clear();
	sourcePoint.clear();
}
var wmsRaster;
function showResults(){
	map.removeLayer(wmsRaster);
	wmsRaster = new ol.layer.Image({
		source: new ol.source.ImageWMS({
		url: 'http://localhost/cgi-bin/mapserv.exe?map=c:\\osgeo4w\\Apache\\htdocs\\mapfile.map&',
		params: {LAYERS:'mylayer'},
		serverType: 'mapserver'
		})
	});
	wmsRaster.setOpacity(0.75);
	map.addLayer(wmsRaster);
}