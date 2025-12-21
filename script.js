// Attendre que la page soit complètement chargée
document.addEventListener('DOMContentLoaded', function() {

    console.log("JavaScript chargé !");

    // Récupérer les éléments du DOM
    const btnAddClient = document.getElementById('btn-add-client');
    const modal = document.getElementById('modal-add-client');
    const btnClose = document.querySelectorAll('.modal-close');

    // Vérifier que les éléments existent (page clients uniquement)
    if (btnAddClient && modal) {

        // Ouvrir le modal quand on clique sur "Ajouter un client"
        btnAddClient.addEventListener('click', function() {
            modal.showModal();
            console.log("Modal ouvert !");
        });

        // Fermer le modal avec les boutons .modal-close
        btnClose.forEach(function(btn) {
            btn.addEventListener('click', function() {
                modal.close();
                console.log("Modal fermé !");
            });
        });

        // Fermer le modal en cliquant sur le backdrop (fond sombre)
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.close();
                console.log("Modal fermé via backdrop !");
            }
        });

        // Gérer la soumission du formulaire
        const form = modal.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                // Ici tu pourras ajouter la logique pour sauvegarder le client
                console.log("Formulaire soumis !");
                modal.close();
            });
        }
    }

});
