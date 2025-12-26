// ========================================
// SCRIPTS PARA FILTROS DE MASCOTAS Y RESCATE
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    inicializarFiltros();
});

function inicializarFiltros() {
    const botones = document.querySelectorAll('.filtro-menu button');
    const mascotasCards = document.querySelectorAll('.mascota-card');
    const rescateCards = document.querySelectorAll('.rescate-card');

    if (botones.length === 0) return;

    botones.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // NO prevenir el comportamiento default ni detener propagación
            
            // Quitar clase activa de todos los botones
            botones.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filtro = this.getAttribute('data-filter');

            // Filtrar tarjetas de mascotas
            mascotasCards.forEach(card => {
                const tipo = card.getAttribute('data-tipo');
                
                if (filtro === 'todos' || tipo === filtro) {
                    card.style.display = 'block';
                    card.classList.remove('oculto');
                } else {
                    card.style.display = 'none';
                    card.classList.add('oculto');
                }
            });

            // Filtrar tarjetas de rescate
            rescateCards.forEach(card => {
                const tipo = card.getAttribute('data-tipo');
                
                if (filtro === 'todos' || tipo === filtro) {
                    card.style.display = 'block';
                    card.classList.remove('oculto');
                } else {
                    card.style.display = 'none';
                    card.classList.add('oculto');
                }
            });

            // Actualizar contador si existe
            actualizarContador();
        });
    });
}

// Función para contar mascotas visibles
function contarMascotasVisibles() {
    const cards = document.querySelectorAll('.mascota-card:not(.oculto), .rescate-card:not(.oculto)');
    return cards.length;
}

// Actualizar contador si existe
function actualizarContador() {
    const contador = document.getElementById('mascotas-contador');
    if (contador) {
        contador.textContent = contarMascotasVisibles();
    }
}