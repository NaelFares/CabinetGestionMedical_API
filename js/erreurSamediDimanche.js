document.addEventListener('DOMContentLoaded', function() {
var inputDate = document.getElementById('date_consultation');
var messageElement = document.getElementById('message-erreur-date');

inputDate.addEventListener('input', function() {
    var selectedDate = new Date(this.value);
    var dayOfWeek = selectedDate.getDay(); // 0 = dimanche, 1 = lundi, ..., 6 = samedi

    // Désactiver les samedis (6) et dimanches (0)
    if (dayOfWeek === 0 || dayOfWeek === 6) {
    messageElement.innerHTML = "Les samedis et dimanches ne sont pas autorisés.<br>Veuillez sélectionner une autre date.";
    this.value = ''; // Effacer la date sélectionnée
    } else {
    messageElement.innerHTML = ''; // Effacer le message si la date est autorisée
    }
});
});
