// Attendez que le DOM (Document Object Model) soit complètement chargé avant d'exécuter le code
document.addEventListener('DOMContentLoaded', function () {
    // Sélectionnez l'élément avec la classe 'patient-select' dans le DOM
    var patientSelect = document.querySelector('.patient-select');
    // Sélectionnez l'élément avec l'ID 'MedecinRef' dans le DOM
    var medecinRefOption = document.getElementById('MedecinRef');

    // Ajoutez un écouteur d'événements pour détecter les changements dans la sélection du patient
    patientSelect.addEventListener('change', function () {
        // Récupérez la valeur sélectionnée dans la liste déroulante des patients
        var selectedPatientId = patientSelect.value;

        // Utilisation de fetch pour effectuer une requête AJAX vers le serveur
        fetch('module/selectMedecinParPatient.php', {
            method: 'POST', // Méthode HTTP utilisée pour la requête
            headers: {
                'Content-type': 'application/x-www-form-urlencoded' // Type de contenu de la requête
            },
            body: 'patientId=' + selectedPatientId // Corps de la requête, contenant l'ID du patient sélectionné
        })
        // Gérez la réponse en tant que texte
        .then(response => response.text())
        // Manipulez le texte HTML de la réponse
        .then(htmlOptions => {
            // Utilisez une expression régulière pour extraire les informations du médecin référent
            var medecinRefMatch = htmlOptions.match(/value="([^"]*)">([^<]*) ([^<]*) ([^<]*)<\/option>/);
            
            // Si la correspondance est réussie (c'est-à-dire que les informations sont extraites avec succès)
            if (medecinRefMatch) {
                // Mettez à jour la valeur de l'option MedecinRef avec l'ID du médecin
                medecinRefOption.value = medecinRefMatch[1];
                // Mettez à jour le texte de l'option MedecinRef avec la civilité, le nom et le prénom du médecin
                // Utilisation de ` `
                medecinRefOption.textContent = `${medecinRefMatch[2]} ${medecinRefMatch[3]} ${medecinRefMatch[4]} (Référent)`;
            }
        })
        // Gérez les erreurs potentielles de la requête AJAX
        .catch(error => console.error('Erreur lors de la requête AJAX:', error));
    });
});
