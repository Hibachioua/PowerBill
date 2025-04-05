
document.addEventListener('DOMContentLoaded', function() {
    // Animation pour les sections
    const sections = document.querySelectorAll('.about, .services .col-md-4, .tarifs .tarif-card, .support .support-feature');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    }, {
        threshold: 0.1
    });
    
    sections.forEach(section => {
        observer.observe(section);
    });
    
    // Défilement doux pour les ancres
    document.querySelectorAll('a[href^="index.php#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Extraire l'identifiant de l'ancre de l'URL
            const targetId = this.getAttribute('href').split('#')[1];
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                // Calculer la position de défilement
                const headerHeight = document.querySelector('header').offsetHeight;
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                
                // Défilement doux
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
const consumptionRange = document.getElementById('consumption-range');
const consumptionDisplay = document.getElementById('consumption-display');
const priceValue = document.getElementById('price-value');
const monthlyTotal = document.getElementById('monthly-total');
const gaugePointer = document.getElementById('gauge-pointer');

function updateConsumption() {
    const value = consumptionRange.value;
    consumptionDisplay.textContent = value;
    
    // Positionner le pointeur
    const percentage = value / 250 * 100;
    gaugePointer.style.left = `${percentage}%`;
    
    // Calculer le prix unitaire
    let price;
    if (value <= 100) {
        price = 0.82;
    } else if (value <= 150) {
        price = 0.92;
    } else {
        price = 1.1;
    }
    
    // Mettre à jour l'affichage du prix
    priceValue.textContent = price.toFixed(2);
    
    // Calculer et afficher l'estimation mensuelle
    const total = (price * value).toFixed(2);
    monthlyTotal.textContent = total;
}

// Initialiser l'affichage
updateConsumption();

// Ajouter l'écouteur d'événement pour le changement de valeur
consumptionRange.addEventListener('input', updateConsumption);

// Animation pour les éléments au scroll
const elements = document.querySelectorAll('.tarifs-info, .consumption-slider, .tarif-box');

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate');
        }
    });
}, {
    threshold: 0.1
});

elements.forEach(element => {
    observer.observe(element);
});
});