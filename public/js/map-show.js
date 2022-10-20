let latitude = document.getElementById("latitudeId").textContent;
let longitude = document.getElementById("longitudeId").textContent;

let map = L.map('map').setView([latitude, longitude], 13);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

let greenIcon = L.icon({
    iconUrl: '/img/leaf-green.png',
    shadowUrl: '/img/leaf-shadow.png',

    iconSize: [38, 95], // size of the icon
    shadowSize: [50, 64], // size of the shadow
    iconAnchor: [22, 94], // point of the icon which will correspond to marker's location
    shadowAnchor: [4, 62],  // the same for the shadow
    popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor
});
L.marker([latitude, longitude], {icon: greenIcon}).addTo(map);


map.on('click', onMapClick);