// for (let pas = 0; pas < 2; pas++) {
//
//     let dateCloture = document.getElementById("dateCloture").innerText;
//     // document.getElementById("demo").innerHTML = dateCloture;
//     document.getElementById("demo").innerHTML = dateCloture;
//
//     // Date fin du coutdown
//     // let dateDeCloture = new Date("{{ sortie.dateCloture ? sortie.dateCloture|date('Y-m-d H:i:s') : '' }}").getTime();
//
//     // Interval de 1 seconde pour le cd
//     let x = setInterval(function() {
//
//         // Date d'aujourd'hui
//         let now = new Date().getTime();
//
//         // distance de compteur
//         let distance = dateCloture - now;
//
//         // Calcul du cd
//         let days = Math.floor(distance / (1000 * 60 * 60 * 24));
//         let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
//         let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
//         let seconds = Math.floor((distance % (1000 * 60)) / 1000);
//
//         if(days === 0 && hours < 1 ) {
//             // Affichage du cd
//             document.getElementById("{{ sortie.id }}").innerHTML = minutes + "m " + seconds + "s ";
//             document.getElementById("{{ sortie.id }}").style.color = 'green';
//         }
//         // Si le cd est = 0
//         else  {
//             // {#clearInterval(x);#}
//             // {#document.getElementById({{ sortie.id }}).innerHTML = "";#}
//         }
//     }, 1000);
// }