// Attendre que la page soit complètement chargée
document.addEventListener('DOMContentLoaded', function() {
    
    console.log("JavaScript chargé ! 🔥");
    
    // Récupérer les éléments du DOM
    const btnAddClient = document.getElementById('btn-add-client');
    const modal = document.getElementById('modal-add-client');
    const btnClose = document.querySelectorAll('.modal-close');
    const overlay = document.querySelector('.modal-overlay');
    
    // Ouvrir le modal quand on clique sur "Ajouter un client"
    btnAddClient.addEventListener('click', function() {
        modal.style.display = 'block';
        console.log("Modal ouvert !");
    });
    
    // Fermer le modal avec le bouton X
    btnClose.forEach(function(btn) {
        btn.addEventListener('click', function() {
            modal.style.display = 'none';
            console.log("Modal fermé !");
        });
    });
    
    // Fermer le modal en cliquant sur l'overlay (fond sombre)
    overlay.addEventListener('click', function() {
        modal.style.display = 'none';
        console.log("Modal fermé via overlay !");
    });
    
});