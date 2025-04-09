window.onload = function() {
    console.log("Fenêtre chargée, démarrage de l'initialisation des graphiques");
    
    setTimeout(function() {
        // Vérifier que Chart.js est disponible
        if (typeof Chart === 'undefined') {
            console.error("Chart.js n'est pas chargé");
            alert("Erreur: Chart.js n'est pas disponible. Veuillez vérifier la console.");
            return;
        }
        
        console.log("Chart.js est disponible:", Chart.version);
        
        // Données communes
        const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        
        try {
            // Graphique 1: Consommation Mensuelle
            const ctx1 = document.getElementById('chart1');
            if (!ctx1) {
                console.error("Canvas 'chart1' non trouvé");
            } else {
                new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Consommation (kWh)',
                            data: [140, 160, 170, 1810, 0, 0, 0, 0, 0, 0, 0, 0],
                            fill: false,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
                console.log("Graphique 1 initialisé");
            }
            
            // Graphique 2: Nouveaux Clients
            const ctx2 = document.getElementById('chart2');
            if (!ctx2) {
                console.error("Canvas 'chart2' non trouvé");
            } else {
                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Nouveaux Clients',
                            data: [2, 1, 3, 5, 0, 0, 0, 0, 0, 0, 0, 0],
                            backgroundColor: 'rgba(54, 162, 235, 0.5)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                console.log("Graphique 2 initialisé");
            }
            
            // Graphique 3: Statuts
            const ctx3 = document.getElementById('chart3');
            if (!ctx3) {
                console.error("Canvas 'chart3' non trouvé");
            } else {
                new Chart(ctx3, {
                    type: 'doughnut',
                    data: {
                        labels: ['Normal', 'Anomalie'],
                        datasets: [{
                            data: [8, 4],
                            backgroundColor: [
                                'rgb(75, 192, 192)',
                                'rgb(255, 99, 132)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
                console.log("Graphique 3 initialisé");
            }
            
            console.log("Tous les graphiques ont été initialisés avec succès");
        } catch (error) {
            console.error("Erreur lors de l'initialisation des graphiques:", error);
            alert("Erreur lors de l'initialisation des graphiques. Vérifiez la console pour plus de détails.");
        }
    }, 500); // Délai de 500ms
};

document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner l'avatar de l'utilisateur
    const userAvatar = document.querySelector('.user-avatar');
    
    // Ajouter un gestionnaire d'événement au clic
    userAvatar.addEventListener('click', function() {
        // Ouvrir le modal
        const userModal = new bootstrap.Modal(document.getElementById('userProfileModal'));
        userModal.show();
        
        // Charger les données du profil via AJAX
        fetch('../../Traitement/get_profile.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Mettre à jour le contenu du modal
                    document.getElementById('userCompanyName').textContent = data.user.nom_fournisseur;
                    document.getElementById('userEmail').textContent = data.user.email;
                    document.getElementById('userFournisseurId').textContent = data.user.id_fournisseur;
                    document.getElementById('userId').textContent = data.user.id;
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des données:', error);
                alert('Une erreur est survenue lors du chargement des données: ' + error.message);
            });
    });
});