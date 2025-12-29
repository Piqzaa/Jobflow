// Attendre que la page soit complètement chargée
document.addEventListener('DOMContentLoaded', function() {
    
    console.log("JavaScript chargé ! 🔥");
    
    // Récupérer les éléments du DOM
    const btnAddClient = document.getElementById('btn-add-client');
    const modal = document.getElementById('modal-add-client');
    const btnClose = document.querySelectorAll('.modal-close');
    const overlay = document.querySelector('.modal-overlay');
    
    // Ouvrir le modal quand on clique sur "Ajouter un client"
    if (btnAddClient) {
        btnAddClient.addEventListener('click', function() {
            modal.style.display = 'block';
            console.log("Modal ouvert !");
        });
    }
    
    // Fermer le modal avec le bouton X
    if (btnClose) {
        btnClose.forEach(function(btn) {
            btn.addEventListener('click', function() {
                modal.style.display = 'none';
                console.log("Modal fermé !");
            });
        });
    }
    
    // Fermer le modal en cliquant sur l'overlay (fond sombre)
    if (overlay) {
        overlay.addEventListener('click', function() {
            modal.style.display = 'none';
            console.log("Modal fermé via overlay !");
        });
    }
    
    // Données de CA par mois (en dur pour l'instant)
    const caParMois = {
        janvier: 200,
        fevrier: 6800,
        mars: 7200,
        avril: 100,
        mai: 6500,
        juin: 7800,
        juillet: 8900,
        aout: 5600,
        septembre: 7400,
        octobre: 6900,
        novembre: 0,
        decembre: 0
    };

    // Calcul du CA annuel
    const caAnnuel = Object.values(caParMois).reduce((total, ca) => total + ca, 0);

    // Plafond micro-entreprise
    const plafond = 77700;
    const pourcentage = (caAnnuel / plafond * 100).toFixed(1);

    // Alerte si on dépasse 80% du plafond
    if (caAnnuel >= plafond * 0.8) {
        const div = document.createElement('div');
        div.className = 'alert-warning';
        div.innerHTML = `⚠️ Attention ! Vous avez atteint ${pourcentage}% du plafond micro-entreprise (${caAnnuel.toLocaleString('fr-FR')}€ / ${plafond.toLocaleString('fr-FR')}€)`;
        
        const content = document.querySelector('.content');
        if (content) {
            content.prepend(div);
        }
    }

    // Affichage du CA annuel
    const caAnnuelEl = document.getElementById('ca-annuel');
    if (caAnnuelEl) {
        caAnnuelEl.textContent = caAnnuel.toLocaleString('fr-FR') + '€';
    }

    // Graphique avec CA par mois
    const canvas = document.getElementById('monGraphique');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [{
                    label: 'Chiffre d\'affaires mensuel',
                    data: Object.values(caParMois),
                    borderColor: 'rgba(99, 102, 241, 1)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: `CA Annuel : ${caAnnuel.toLocaleString('fr-FR')}€ / ${plafond.toLocaleString('fr-FR')}€ (${pourcentage}%)`,
                        font: { size: 16, weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('fr-FR') + '€';
                            }
                        }
                    }
                }
            }
        });
    }

}); 