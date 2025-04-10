// IHM/assets/js/history.js
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour obtenir les paramètres de l'URL
    function getUrlParams() {
        const params = new URLSearchParams(window.location.search);
        return {
            annee: params.get('annee') || '',
            mois: params.get('mois') || '',
            client: params.get('client') || '',
            api: 'true'
        };
    }
    
    // Formater l'URL avec les paramètres
    function formatUrl(params) {
        const urlParams = new URLSearchParams();
        for (const key in params) {
            if (params[key] && key !== 'api') {
                urlParams.append(key, params[key]);
            }
        }
        return urlParams.toString();
    }
    
    // Charger les données depuis l'API
    function loadData() {
        const params = getUrlParams();
        
        // Afficher un indicateur de chargement
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="9" class="text-center"><i class="fas fa-spinner fa-spin"></i> Chargement des données...</td></tr>';
        
        // Correction du chemin vers le fichier de traitement
        fetch("../../Traitement/history_traitement.php?" + new URLSearchParams(params))
            .then(response => {
                console.log("Statut de la réponse:", response.status);
                if (!response.ok) {
                    throw new Error('Erreur réseau: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log("Données reçues:", data);
                
                // Remplir les options des filtres
                populateFilterOptions(data);
                
                // Afficher ou masquer le bouton de réinitialisation
                document.getElementById('resetFilters').style.display = data.hasFilters ? 'block' : 'none';
                
                // Afficher les badges de filtres
                renderFilterBadges(data);
                
                // Remplir le tableau
                renderTable(data);
                
                // Initialiser le modal pour les images
                initImageModal();
            })
            .catch(error => {
                console.error('Erreur lors du chargement des données:', error);
                document.getElementById('alert-container').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Erreur lors du chargement des données: ${error.message}
                    </div>
                `;
                document.getElementById('tableBody').innerHTML = '';
            });
    }
    
    // Remplir les options des filtres
    function populateFilterOptions(data) {
        // Options des années
        const anneeSelect = document.getElementById('annee');
        anneeSelect.innerHTML = '<option value="">Toutes les années</option>';
        data.years.forEach(year => {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            if (data.filtres.annee == year) {
                option.selected = true;
            }
            anneeSelect.appendChild(option);
        });
        
        // Options des mois
        const moisSelect = document.getElementById('mois');
        moisSelect.innerHTML = '<option value="">Tous les mois</option>';
        for (const [key, value] of Object.entries(data.mois)) {
            const option = document.createElement('option');
            option.value = key;
            option.textContent = value;
            if (data.filtres.mois == key) {
                option.selected = true;
            }
            moisSelect.appendChild(option);
        }
        
        // Options des clients
        const clientSelect = document.getElementById('client');
        clientSelect.innerHTML = '<option value="">Tous les clients</option>';
        data.clients.forEach(client => {
            const option = document.createElement('option');
            option.value = client.ID_Client;
            option.textContent = `${client.Nom} ${client.Prenom} (${client.CIN})`;
            if (data.filtres.client == client.ID_Client) {
                option.selected = true;
            }
            clientSelect.appendChild(option);
        });
    }
    
    // Afficher les badges des filtres actifs
    function renderFilterBadges(data) {
        const badgesContainer = document.getElementById('filterBadges');
        badgesContainer.innerHTML = '';
        
        if (!data.hasFilters) {
            return;
        }
        
        // Badge pour l'année
        if (data.filtres.annee) {
            const badge = document.createElement('div');
            badge.className = 'filter-badge';
            
            const params = {...data.filtres, annee: ''};
            const url = formatUrl(params);
            
            badge.innerHTML = `
                Année: ${data.filtres.annee}
                <a href="?${url}">
                    <i class="fas fa-times-circle"></i>
                </a>
            `;
            badgesContainer.appendChild(badge);
        }
        
        // Badge pour le mois
        if (data.filtres.mois) {
            const badge = document.createElement('div');
            badge.className = 'filter-badge';
            
            const params = {...data.filtres, mois: ''};
            const url = formatUrl(params);
            
            badge.innerHTML = `
                Mois: ${data.mois[data.filtres.mois]}
                <a href="?${url}">
                    <i class="fas fa-times-circle"></i>
                </a>
            `;
            badgesContainer.appendChild(badge);
        }
        
        // Badge pour le client
        if (data.filtres.client) {
            let clientName = '';
            for (const client of data.clients) {
                if (client.ID_Client == data.filtres.client) {
                    clientName = `${client.Nom} ${client.Prenom}`;
                    break;
                }
            }
            
            const badge = document.createElement('div');
            badge.className = 'filter-badge';
            
            const params = {...data.filtres, client: ''};
            const url = formatUrl(params);
            
            badge.innerHTML = `
                Client: ${clientName}
                <a href="?${url}">
                    <i class="fas fa-times-circle"></i>
                </a>
            `;
            badgesContainer.appendChild(badge);
        }
    }
    
    // Remplir le tableau des consommations
    function renderTable(data) {
        const tableBody = document.getElementById('tableBody');
        tableBody.innerHTML = '';
    
        if (data.consommations.length === 0) {
            document.getElementById('alert-container').innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucune consommation trouvée.
                    ${data.hasFilters ? 'Essayez de modifier vos filtres ou <a href="history.php">réinitialisez-les</a>.' : ''}
                </div>
            `;
            document.querySelector('.table-responsive').style.display = 'none';
            document.querySelector('.pagination-container').style.display = 'none';
            return;
        }
    
        document.getElementById('alert-container').innerHTML = '';
        document.querySelector('.table-responsive').style.display = 'block';
        document.querySelector('.pagination-container').style.display = 'flex';
    
        // Remplir les lignes du tableau
        data.consommations.forEach(conso => {
            const row = document.createElement('tr');
    
            row.innerHTML = `
                <td>${conso.ID_Consommation}</td>
                <td>${escapeHtml(conso.Nom)} ${escapeHtml(conso.Prenom)}</td>
                <td>${escapeHtml(conso.CIN)}</td>
                <td>${escapeHtml(conso.Email)}</td>
                <td>${data.mois[conso.Mois]} ${conso.Annee}</td>
                <td>${conso.ID_Compteur}</td>
                <td>${conso.Qté_consommé} kWh</td>
                <td>
                    ${conso.status === "pas d'anomalie" ? 
                        '<span class="status-badge status-normal">Normal</span>' : 
                        '<span class="status-badge status-anomalie">Anomalie</span>'}
                </td>
            `;
            
            tableBody.appendChild(row);
        });
    
        // Pagination simplifiée (statique pour cet exemple)
        document.getElementById('pagination').innerHTML = `
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Précédent</a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
                <a class="page-link" href="#">Suivant</a>
            </li>
        `;
    }
    
    
    // Fonction pour échapper les caractères HTML (sécurité)
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    // Initialiser le modal pour les images
    function initImageModal() {
        const imageModal = document.getElementById('imageModal');
        if (imageModal) {
            imageModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const title = button.getAttribute('data-title');
                const imageSrc = button.getAttribute('data-image');
                
                const modalTitle = imageModal.querySelector('.modal-title');
                const modalImage = document.getElementById('modalImage');
                
                modalTitle.textContent = title;
                modalImage.src = imageSrc;
            });
        }
    }
    
    // Fonction de recherche dans le tableau
    window.filterTable = function() {
        const input = document.getElementById("searchInput");
        const filter = input.value.toUpperCase();
        const table = document.getElementById("dataTable");
        const tr = table.getElementsByTagName("tr");
        
        // Ignorer l'en-tête (commencer à 1)
        for (let i = 1; i < tr.length; i++) {
            let found = false;
            const td = tr[i].getElementsByTagName("td");
            
            for (let j = 0; j < td.length; j++) {
                if (td[j]) {
                    const txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            
            tr[i].style.display = found ? "" : "none";
        }
    };
    
    // Écouter la soumission du formulaire
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const annee = document.getElementById('annee').value;
        const mois = document.getElementById('mois').value;
        const client = document.getElementById('client').value;
        
        const params = { annee, mois, client };
        const url = formatUrl(params);
        
        window.location.href = `?${url}`;
    });
    
    // Écouter le clic sur le bouton de réinitialisation
    document.getElementById('resetFilters').addEventListener('click', function() {
        window.location.href = 'history.php';
    });
    
    // Charger les données au chargement de la page
    loadData();
});