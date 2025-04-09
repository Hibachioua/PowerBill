let allReclamations = [];
    const descriptionModal = new bootstrap.Modal(document.getElementById('descriptionModal'));
    const traitementModal = new bootstrap.Modal(document.getElementById('traitementModal'));
    let currentFilters = {
        client: '',
        statut: '',
        type: '',
        date: ''
    };

    function renderTable(data) {
        const tbody = document.querySelector(".table-custom tbody");
        tbody.innerHTML = "";

        let traiteCount = 0;
        let encoursCount = 0;

        data.forEach(r => {
            const isTraite = r.Statut === "Traité";
            
            if (isTraite) traiteCount++;
            else encoursCount++;

            const badge = isTraite
                ? `<span class="badge-approved">Traitée</span>`
                : `<span class="badge-pending">En cours</span>`;

            const actions = isTraite
                ? `<button class="action-btn" onclick="showDescription('${r.Description.replace(/'/g, "\\'")}')">
                      <i class="bi bi-eye"></i> View
                   </button>`
                : `<button class="action-btn" onclick="openTraitementModal('${r.ID_Réclamation}', '${r.Description.replace(/'/g, "\\'")}')">
                      <i class="bi bi-pencil"></i> Traiter
                   </button>
                   <button class="action-btn" onclick="showDescription('${r.Description.replace(/'/g, "\\'")}')">
                      <i class="bi bi-eye"></i> View
                   </button>`;

            const row = `
                <tr>
                    <td>${r.Nom || 'N/A'}</td>
                    <td>${badge}</td>
                    <td>${r.Type_Réclamation || 'N/A'}</td>
                    <td>${r.Date_Réclamation || 'N/A'}</td>
                    <td>${actions}</td>
                </tr>`;
            tbody.innerHTML += row;
        });

        document.getElementById("count-traite").innerText = traiteCount;
        document.getElementById("count-encours").innerText = encoursCount;
    }

    function applyFilters() {
        let filtered = [...allReclamations];

        // Apply client filter
        if (currentFilters.client) {
            filtered = filtered.filter(r => 
                (r.Nom || '').toLowerCase().includes(currentFilters.client.toLowerCase())
            );
        }

        // Apply status filter
        if (currentFilters.statut) {
            filtered = filtered.filter(r => r.Statut === currentFilters.statut);
        }

        // Apply type filter
        if (currentFilters.type) {
            filtered = filtered.filter(r => r.Type_Réclamation === currentFilters.type);
        }

        // Apply date filter
        if (currentFilters.date) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            filtered = filtered.filter(r => {
                const recDate = new Date(r.Date_Réclamation);
                
                switch(currentFilters.date) {
                    case 'today':
                        return recDate.toDateString() === today.toDateString();
                    case 'week':
                        const weekStart = new Date(today);
                        weekStart.setDate(today.getDate() - today.getDay());
                        return recDate >= weekStart;
                    case 'month':
                        return recDate.getMonth() === today.getMonth() && 
                               recDate.getFullYear() === today.getFullYear();
                    default:
                        return true;
                }
            });
        }

        renderTable(filtered);
    }

    function showDescription(description) {
        document.getElementById('modal-description-content').textContent = description;
        descriptionModal.show();
    }

    function openTraitementModal(id, description) {
        document.getElementById('modal-reclamation-id').value = id;
        document.getElementById('modal-description').value = description;
        document.getElementById('modal-reponse').value = '';
        traitementModal.show();
    }

    function setupFilterDropdowns() {
        document.querySelectorAll('[data-filter]').forEach(header => {
            const filterType = header.getAttribute('data-filter');
            const dropdown = header.querySelector('.filter-dropdown');
            
            header.addEventListener('click', (e) => {
                if (e.target.classList.contains('filter-option') || 
                    e.target.classList.contains('form-control')) {
                    return;
                }
                
                // Close all other dropdowns
                document.querySelectorAll('.filter-dropdown').forEach(d => {
                    if (d !== dropdown) d.classList.remove('show');
                });
                
                dropdown.classList.toggle('show');
            });

            // Handle option selection
            dropdown.querySelectorAll('.filter-option').forEach(option => {
                option.addEventListener('click', () => {
                    dropdown.querySelectorAll('.filter-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    option.classList.add('selected');
                    
                    currentFilters[filterType] = option.getAttribute('data-value') || '';
                    applyFilters();
                    
                    // Close dropdown after selection
                    setTimeout(() => dropdown.classList.remove('show'), 200);
                });
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('[data-filter]')) {
                document.querySelectorAll('.filter-dropdown').forEach(d => {
                    d.classList.remove('show');
                });
            }
        });
    }

    document.getElementById('form-traitement').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('modal-reclamation-id').value;
        const reponse = document.getElementById('modal-reponse').value;
        
        const formData = new FormData();
        formData.append('id_reclamation', id);
        formData.append('reponse', reponse);
        
        console.log('Envoi des données:', {id, reponse}); // Debug
        
        fetch('../../Traitement/reclamation_fournisseur.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Réponse reçue:', response); // Debug
            if (!response.ok) {
                return response.text().then(text => { throw new Error(text) });
            }
            return response.json();
        })
        .then(data => {
            console.log('Données reçues:', data); // Debug
            if (data.success) {
                traitementModal.hide();
                allReclamations = data.reclamations;
                renderTable(allReclamations);
                document.getElementById("count-traite").innerText = data.stats.traitees;
                document.getElementById("count-encours").innerText = data.stats.encours;
            } else {
                throw new Error(data.error || 'Erreur inconnue');
            }
        })
        .catch(error => {
            console.error('Erreur complète:', error); // Debug
        });
    });

    function loadReclamations() {
        fetch('../../Traitement/reclamation_fournisseur.php')
            .then(response => response.json())
            .then(data => {
                allReclamations = data.reclamations || data;
                renderTable(allReclamations);
                setupFilterDropdowns();

                if (data.stats) {
                    document.getElementById("count-traite").innerText = data.stats.traitees;
                    document.getElementById("count-encours").innerText = data.stats.encours;
                }

                // Populate client filter options - VERSION CORRIGÉE
                const clientFilter = document.getElementById('client-filter');
                // Clear existing options except the first one ("Tous")
                while (clientFilter.children.length > 2) {
                    clientFilter.removeChild(clientFilter.lastChild);
                }
                
                // Get unique client names
                const clients = [...new Set(allReclamations.map(r => {
                    return r.Nom || 'N/A';
                }))];
                
                // Add new options
                clients.forEach(client => {
                    if (client !== 'N/A') {
                        const option = document.createElement('div');
                        option.className = 'filter-option';
                        option.setAttribute('data-value', client);
                        option.textContent = client;
                        clientFilter.appendChild(option);
                    }
                });

                // Add search functionality
                const clientSearch = clientFilter.querySelector('input');
                clientSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const options = clientFilter.querySelectorAll('.filter-option:not(:first-child)');
                    
                    options.forEach(option => {
                        const text = option.textContent.toLowerCase();
                        option.style.display = text.includes(searchTerm) ? 'block' : 'none';
                    });
                });
            })
            .catch(error => console.error("Erreur :", error));
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadReclamations();
    });