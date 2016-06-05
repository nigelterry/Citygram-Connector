/**
 * Created by Nigel J. Terry on 3/20/16.
 */

var markers = new L.LayerGroup().addTo(map);

function loadRelated() {
    map.off('zoomend dragend resize', loadRelated);
    var bounds = map.getBounds();
    var request = {};
    request[searchModel] = {
        'ne_lat' : bounds._northEast.lat,
        'ne_long' : bounds._northEast.lng,
        'sw_lat' : bounds._southWest.lat,
        'sw_long' : bounds._southWest.lng,
        'ignore' : id,
        'limit' : query_limit};
    $.post( 'related',
        request ,
        drawRelated,
        'json');
}

function drawRelated(relates){
    markers.clearLayers();
    L.geoJson(relates, {
        onEachFeature: function(feature, layer) {
            // does this feature have a property named popupContent?
            this.color = '#F00';
            this.fill = false;
            if (feature.properties && feature.properties.popupContent) {
                layer.bindPopup(feature.properties.popupContent, {className : 'secondary-popup', autoPan  : true});
            }
        },
        pointToLayer: function (feature, latlng) {
            return L.circleMarker(latlng, geojsonMarkerOptions);
        },
    }).addTo(markers).bringToBack();
    map.once('zoomend dragend resize', loadRelated);
    marker.once('onclick', popupMarker);
}

function popupMarker(){
    marker.openPopup();
}

var geojsonMarkerOptions = {
    radius: 8,
    fillColor: "red",
    color: "#000",
    weight: 1,
    opacity: 1,
    fillOpacity: 0.5
};

function addEvents(){
    tiles.off('loading', addEvents);
    marker.openPopup();
    map.once('zoomend dragend resize', loadRelated);
}