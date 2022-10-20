var map = L.map('map').setView([48.8588897, 2.320041], 13);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

var greenIcon = L.icon({
    iconUrl: 'img/leaf-green.png',
    shadowUrl: 'img/leaf-shadow.png',

    iconSize:     [38, 95], // size of the icon
    shadowSize:   [50, 64], // size of the shadow
    iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
    shadowAnchor: [4, 62],  // the same for the shadow
    popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
});
// L.marker([0, 0], {icon: greenIcon}).addTo(map);

var popup = L.popup();
let coords;
function onMapClick(e) {

    var regExpLat = /(?<=\().+?(?=,)/g;
    var latitude = regExpLat.exec(e.latlng);

    var regExpLong = /(?<=,).+?(?=\))/g;
    var longitude = regExpLong.exec(e.latlng);

    popup
        .setLatLng(e.latlng)
        .setContent("Coords GPS de la sortie :" + latitude+", "+longitude)
        .openOn(map);

    // /\[.+?\]/g
    // /(?<=\[).+?(?=\])/g



    document.getElementById("lieu_latitude").value = latitude;
    document.getElementById("lieu_longitude").value = longitude;
}

map.on('click', onMapClick);