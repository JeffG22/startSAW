"use strict";

function setMap() {
    var mymap = L.map('mapid').setView([44.409, 8.918], 14);

    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox.streets',
        accessToken: 'pk.eyJ1IjoiZnJjYXNzaSIsImEiOiJjam91YnNweXMxYWU5M3FxaTlqYjY5bWZtIn0.EnFyo44Gi66qhBhU3DFlTA'
    }).addTo(mymap);
}