// ========================================
// CARGAR CONTADOR DEL CARRITO AL INICIAR
// ========================================
document.addEventListener('DOMContentLoaded', function () {
    actualizarContadorCarrito();

    // Si hay video hero, configurarlo para loop corto
    const video = document.querySelector('.hero-video-bg');
    if (video) {
        video.addEventListener('loadedmetadata', () => {
            const reinicioCada = 10; // segundos
            setInterval(() => {
                if (video.currentTime >= reinicioCada) {
                    video.currentTime = 0;
                    video.play();
                }
            }, 500);
        });
    }
});

// ========================================
// ACTUALIZAR CONTADOR DEL CARRITO
// ========================================
// Helper para obtener la URL base
function getBaseUrl() {
    return document.querySelector('meta[name="app-url"]')?.getAttribute('content') || '';
}

function actualizarContadorCarrito() {
    fetch(`${getBaseUrl()}/carrito/count`)
        .then(response => response.json())
        .then(data => {
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = data.count || 0;
            }
        })
        .catch(error => console.error('Error:', error));
}

// ========================================
// NOTIFICACIONES TOAST (Mensajes emergentes)
// ========================================
function mostrarNotificacion(mensaje, tipo = 'success') {
    // Crear el elemento toast
    const toast = document.createElement('div');
    toast.className = `toast toast-${tipo}`;
    toast.innerHTML = `
        <div class="toast-content">
            <span class="toast-icon">${tipo === 'success' ? '✓' : '⚠'}</span>
            <span class="toast-message">${mensaje}</span>
        </div>
    `;

    // Agregar al body
    document.body.appendChild(toast);

    // Mostrar con animación
    setTimeout(() => toast.classList.add('show'), 100);

    // Ocultar y eliminar después de 3 segundos
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ========================================
// AGREGAR AL CARRITO (desde store.php)
// ========================================
function agregarAlCarrito(idProducto, nombre, precio, imagen) {
    fetch(`${getBaseUrl()}/carrito`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
            id_producto: idProducto,
            cantidad: 1
        })
    })
        .then(async response => {
            if (response.status === 401) {
                return { success: false, require_login: true, message: 'Debes iniciar sesión' };
            }
            if (!response.ok) {
                // Intentar obtener JSON; si falla, devolver error genérico
                try {
                    const text = await response.text();
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Error del servidor:', text);
                        return { success: false, message: 'Error en la solicitud (ver consola)' };
                    }
                } catch (e) {
                    console.error('Error de red:', e);
                    return { success: false, message: 'Error de conexión' };
                }
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.message) {
                    mostrarNotificacion(`⚠ ${data.message}`, 'error');
                } else {
                    mostrarNotificacion(`✓ ${nombre} agregado al carrito`, 'success');
                }
                actualizarContadorCarrito();
            } else {
                if (data.require_login) {
                    mostrarNotificacion('⚠ Debes iniciar sesión para agregar productos', 'error');
                    setTimeout(() => {
                        window.location.href = `${getBaseUrl()}/login`;
                    }, 2000);
                } else {
                    mostrarNotificacion(data.message ? `❌ ${data.message}` : '❌ Error al agregar producto', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('❌ Error de conexión', 'error');
        });
}

// ========================================
// FILTROS DE MASCOTAS/PRODUCTOS
// ========================================
function inicializarFiltros() {
    const botones = document.querySelectorAll('.filtro-menu button');
    const cards = document.querySelectorAll('[data-tipo], [data-filter]');

    botones.forEach(btn => {
        btn.addEventListener('click', function () {
            // Remover clase activa de todos los botones
            botones.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filtro = this.getAttribute('data-filter');

            // Filtrar cards
            cards.forEach(card => {
                const tipo = card.getAttribute('data-tipo') || card.classList;

                if (filtro === 'todos' ||
                    tipo === filtro ||
                    (typeof tipo === 'object' && tipo.contains(filtro))) {
                    card.classList.remove('oculto');
                    card.style.display = '';
                } else {
                    card.classList.add('oculto');
                }
            });
        });
    });
}

// Inicializar filtros si existen
if (document.querySelector('.filtro-menu')) {
    inicializarFiltros();
}

// ========================================
// ANIMACIONES DE SCROLL
// ========================================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function (entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
        }
    });
}, observerOptions);

// Observar elementos que queremos animar
document.querySelectorAll('.pet-card, .mascota-card, .producto-card, .rescate-card').forEach(el => {
    observer.observe(el);
});

// ========================================
// SMOOTH SCROLL PARA ENLACES
// ========================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// ========================================
// MODO OSCURO (BONUS)
// ========================================
function toggleModoOscuro() {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('dark-mode', isDark);
}

// Cargar preferencia de modo oscuro
if (localStorage.getItem('dark-mode') === 'true') {
    document.body.classList.add('dark-mode');
}
