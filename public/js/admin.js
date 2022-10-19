// Get the modal
let modal = document.getElementById("myModal");
let lieuModal = document.getElementById("lieuModal")
let siteModal = document.getElementById("siteModal")
let villeModal = document.getElementById("villeModal")

// Get the button that opens the modal
let btn = document.getElementById("myBtn");
let btnLieu = document.getElementById("lieuMyBtn")
let btnSite = document.getElementById("siteMyBtn")
let btnVille = document.getElementById("villeMyBtn")

// Get the <span> element that closes the modal
let span = document.getElementsByClassName("closeUser")[0];
let spanLieu = document.getElementsByClassName("closeLieu")[0];
let spanSite = document.getElementsByClassName("closeSite")[0];
let spanVille = document.getElementsByClassName("closeVille")[0];


// When the user clicks on the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
}
btnLieu.onclick = function () {
    lieuModal.style.display = "block";
}
btnSite.onclick = function () {
    siteModal.style.display = "block";
}
btnVille.onclick = function () {
    villeModal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}
spanSite.onclick = function() {
    siteModal.style.display = "none";
}
spanLieu.onclick = function() {
    lieuModal.style.display = "none";
}
spanVille.onclick = function() {
    villeModal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
// window.onclick = function(event) {
//     if (event.target == lieuModal) {
//         lieuModal.style.display = "none";
//     }
// }
// window.onclick = function(event) {
//     if (event.target == siteModal) {
//         siteModal.style.display = "none";
//     }
// }
