<div class="content">
    <div id="map" style="width: 100%; height: 530px; color:black;"></div>
</diV>
<script>
    var ibukota = new L.LayerGroup();
    var sungai = new L.LayerGroup();
    var wilayah = new L.LayerGroup();
    var map = L.map('map',{
        center: [-7.8000456777, 110.39128023],
        zoom: 9,
        zoomControl: false,
        layers:[]
    })

    // Base Map 1
    var GoogleSatelliteHybrid= L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',{
        maxZoom:22,
        attribution: 'Google Satellite'
    }).addTo(map);

    // Base Map 2
    var OpenStreetMap_Mapnik = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 22,
	attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });

    // Base Map 3
    var GoogleRoads = new L.TileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}',{ 
	opacity: 1.0, 
	attribution: 'Google Roads' 
	});
    
    //Base Map 4
    var GoogleMaps = new L.TileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{ 
	opacity: 1.0, 
	attribution: 'Google Maps' 
	});

    // groupedlayer
    var baseLayers = {'Google Satellite Hybrid': GoogleSatelliteHybrid,
                      'Open Street Map': OpenStreetMap_Mapnik,
                      'Google Maps': GoogleMaps,
                      'Google Roads': GoogleRoads
    };
    var overlayLayers = {'Ibu kota': ibukota,
                         'Sungai': sungai,
                         'Batas Administrasi': wilayah
    }
    L.control.layers(baseLayers, overlayLayers, {collapsed: true}).addTo(map);

    // MiniMap
    var osmUrl='https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
    var osmAttrib='Map data &copy; OpenStreetMap contributors';
    var osm2 = new L.TileLayer(osmUrl, {minZoom: 0, maxZoom: 13, attribution: osmAttrib });
    var rect1 = {color: "#ff1100", weight: 3};
    var rect2 = {color: "#0000AA", weight: 1, opacity:0, fillOpacity:0};
    var miniMap = new L.Control.MiniMap(osm2, {toggleDisplay: true, position : "bottomright", aimingRectOptions : rect1, shadowRectOptions: rect2}).addTo(map);

    // Control Geocoder = Search
    L.Control.geocoder({position :"topleft", collapsed:true}).addTo(map);

    // North Arrow
    var north = L.control({position: "topright"});
        north.onAdd = function(map) {
            var div = L.DomUtil.create("div","info legend");
            div.innerHTML = '<img src="<?=base_url()?>assets/north-arrow.png" width="75">';
            return div;
        }
    north.addTo(map);

    // Locate Control = My Position
    /* GPS enabled geolocation control set to follow the user's location */ 
var locateControl = L.control.locate({ 
	position: "topleft", 
	drawCircle: true, 
	follow: true, 
	setView: true, 
	keepCurrentZoomLevel: true, 
	markerStyle: { 
		weight: 1, 
		opacity: 0.8, 
		fillOpacity: 0.8 
	}, 
	circleStyle: { 
		weight: 1, 
		clickable: false 
	}, 
	icon: "fa fa-location-arrow", 
	metric: false, 
	strings: { 
		title: "My location", 
		popup: "You are within {distance} {unit} from this point", 
		outsideMapBoundsMsg: "You seem located outside the boundaries of the map" 
	}, 
	locateOptions: { 
		maxZoom: 18, 
		watch: true, 
		enableHighAccuracy: true, 
		maximumAge: 10000, 
		timeout: 10000 
	} 
}).addTo(map);

    // Zoom Bar
    var zoom_bar = new L.Control.ZoomBar({position: 'topleft'}).addTo(map);

    // Coordinates
    L.control.coordinates({ 
	    position:"bottomright", 
	    decimals:2, 
	    decimalSeperator:",", 
	    labelTemplateLat:"Latitude: {y}", 
	    labelTemplateLng:"Longitude: {x}" 
	}).addTo(map);

    // Titik Ibu Kota
    $.getJSON("<?=base_url()?>assets/ibukota.geojson",function(data){ 
	var ratIcon = L.icon({ 
		iconUrl: '<?=base_url()?>assets/marker-1.png', 
		iconSize: [10,10] 
	}); 
	L.geoJson(data,{ 
	pointToLayer: function(feature,latlng){ 
		var marker = L.marker(latlng,{icon: ratIcon}); 
		marker.bindPopup(feature.properties.Keterangan); 
		return marker; 
		} 
	}).addTo(ibukota); 
	});

    // Garis Sungai
    $.getJSON("<?=base_url()?>/assets/sungai.geojson",function(kode){ 
	L.geoJson( kode, {
		style: function(feature){ 
			return {color: "#0000FF"}; 
		}, 
		onEachFeature: function( feature, layer ){ 
			layer.bindPopup 
			() 
		} 
	}).addTo(sungai); 
});

    // Poligon Wilayah 
    $.getJSON("<?=base_url()?>/assets/wilayah.geojson",function(kode){ 
	L.geoJson( kode, {
		style: function(feature){ 
			return {color: "#FFF199"}; 
		}, 
		onEachFeature: function( feature, layer ){ 
			layer.bindPopup(feature.properties.Keterangan);
		} 
	}).addTo(wilayah); 
});

	// Legenda
	const legend = L.control.Legend({ 
	position: "bottomleft", 
	collapsed: false, 
	symbolWidth: 24, 
	opacity: 1, 
	column: 2, 
	legends: [{ 
		label: "Ibu Kota", 
		type: "image", 
		url: "<?=base_url()?>/assets/marker-1.png" 
		},{ 
		label: "Sungai", 
		type: "polyline", 
		color: "#0000FF", 
		fillColor: "#0000FF", 
		weight: 2 
		},{
		label: "Batas Wilayah", 
		type: "polygon", 
		sides: 4, 
		color: "#FFF199", 
		fillColor: "#FFF199", 
		weight: 2 
		}] 
	})
.addTo(map);

</script>