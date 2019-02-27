/* funzione per aggiunger popup */
function onEachFeature(feature, layer) {
    // does this feature have a property?
    var content;
    if (feature.properties) {
        content = feature.properties.location+"<br>"+
                    feature.properties.info+"<br>"+
                    feature.properties.street+"<br>"+
                    feature.properties.city;
    }
    layer.bindPopup(content);
}

function ajaxSuccess(data, textStatus, jqXHR) {
    //alert(textStatus);
    var payload = JSON.parse(jqXHR.responseText);
    popup
        .setContent(payload["location"]["localtime"]+
                    "<br>Località: <b>"+payload["location"]["name"]+
                    "<br></b>Temperatura: "+payload["current"]["temp_c"]+" °C"+
                    "<br>Percepita: "+payload["current"]["feelslike_c"]+" °C"
                )
        .openOn(mymap);
}
function ajaxError(jqXHR, textStatus, errorThrown) {
    popup
        .setContent(textStatus+" "+errorThrown)
        .openOn(mymap);
}
function onMapOpen(e) {
    //alert(typeof e.target);
    return;
}

function onMapClick(e) {
    popup.setLatLng(e.latlng);
    var LatLng = e.latlng.toString();
    // pulire stringa
    var begin = LatLng.indexOf("(");
    var end = LatLng.indexOf(")");
    var coords = LatLng.substring(begin+1,end).replace(" ","");

    $.ajax({
        url: "http://api.apixu.com/v1/current.json?key=b79bfc73cf2f4f08854173333181611&q="+coords,
        success: ajaxSuccess,
        error: ajaxError,
        async: true
    }).always(function() {});
}