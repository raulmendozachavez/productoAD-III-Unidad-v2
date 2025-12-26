// ========================================
// SCRIPTS DEL PANEL DE ADMINISTRACIÓN
// ========================================

// Confirmar eliminación
function confirmarEliminacion(nombre) {
    return confirm(`¿Estás seguro de eliminar "${nombre}"? Esta acción no se puede deshacer.`);
}

// Confirmar reseteo
function confirmarReseteo(nombre) {
    return confirm(`¿Volver "${nombre}" a estado disponible?`);
}

// Confirmar aprobación
function confirmarAprobacion(usuario, mascota) {
    return confirm(`¿Aprobar la adopción de "${mascota}" para ${usuario}?`);
}

// Confirmar rechazo
function confirmarRechazo(usuario, mascota) {
    return confirm(`¿Rechazar la adopción de "${mascota}" para ${usuario}?`);
}

// Vista previa de imagen
function previsualizarImagen(input) {
    const preview = document.getElementById('image-preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function abrirModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    const scrollY = window.scrollY || window.pageYOffset;
    
    // Mover modal al body si no está ahí
    if (modal.parentNode !== document.body) {
        document.body.appendChild(modal);
    }
    
    // Bloquear scroll del body
    document.body.style.overflow = 'hidden';
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '100%';
    
    const modalContent = modal.querySelector('.modal-content');
    
    // Aplicar estilos del modal content
    if (modalContent) {
        modalContent.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.95);
            margin: 0;
            max-height: min(90vh, 800px);
            max-width: min(95vw, 650px);
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        `;
    }
    
    // Estilos del overlay
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0);
        backdrop-filter: blur(0px);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        box-sizing: border-box;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    `;
    
    // Forzar reflow
    void modal.offsetHeight;
    
    // Activar animación de entrada
    requestAnimationFrame(() => {
        modal.style.background = 'rgba(0, 0, 0, 0.6)';
        modal.style.backdropFilter = 'blur(8px)';
        
        if (modalContent) {
            modalContent.style.opacity = '1';
            modalContent.style.transform = 'translate(-50%, -50%) scale(1)';
        }
    });
}

// Cerrar modal con animación
function cerrarModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    const modalContent = modal.querySelector('.modal-content');
    
    // Animación de salida
    modal.style.background = 'rgba(0, 0, 0, 0)';
    modal.style.backdropFilter = 'blur(0px)';
    
    if (modalContent) {
        modalContent.style.opacity = '0';
        modalContent.style.transform = 'translate(-50%, -50%) scale(0.95)';
    }
    
    // Esperar a que termine la animación
    setTimeout(() => {
        modal.style.display = 'none';
        
        // Restaurar scroll del body
        const scrollY = document.body.style.top;
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.overflow = 'auto';
        
        if (scrollY) {
            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        }
    }, 300);
}

// Cerrar modal al hacer click en el overlay
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        const modalId = event.target.id;
        if (modalId) {
            cerrarModal(modalId);
        }
    }
}, true);

// Cerrar modal con tecla ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modalsAbiertos = document.querySelectorAll('.modal-overlay[style*="display: flex"], .modal-overlay[style*="display: block"]');
        modalsAbiertos.forEach(modal => {
            if (modal.id) {
                cerrarModal(modal.id);
            }
        });
    }
});

// Filtrar tabla
function filtrarTabla(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    const filter = input.value.toLowerCase();
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent || row.innerText;
        
        if (text.toLowerCase().indexOf(filter) > -1) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

// Validar formulario de mascota
function validarFormularioMascota(form) {
    const nombre = form.querySelector('[name="nombre"]').value.trim();
    const tipo = form.querySelector('[name="tipo"]').value;
    const imagen = form.querySelector('[name="imagen"]');
    
    if (!nombre) {
        alert('El nombre es obligatorio');
        return false;
    }
    
    if (!tipo) {
        alert('Debes seleccionar un tipo de mascota');
        return false;
    }
    
    // Validar imagen solo si se está agregando una nueva
    if (imagen && imagen.files.length > 0) {
        const file = imagen.files[0];
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        
        if (!allowedTypes.includes(file.type)) {
            alert('Solo se permiten imágenes (JPG, PNG, GIF)');
            return false;
        }
        
        if (file.size > 5000000) { // 5MB
            alert('La imagen no debe superar los 5MB');
            return false;
        }
    }
    
    return true;
}

// Validar formulario de producto
function validarFormularioProducto(form) {
    const nombre = form.querySelector('[name="nombre"]').value.trim();
    const precio = parseFloat(form.querySelector('[name="precio"]').value);
    const stock = parseInt(form.querySelector('[name="stock"]').value);
    
    if (!nombre) {
        alert('El nombre es obligatorio');
        return false;
    }
    
    if (isNaN(precio) || precio <= 0) {
        alert('El precio debe ser mayor a 0');
        return false;
    }
    
    if (isNaN(stock) || stock < 0) {
        alert('El stock no puede ser negativo');
        return false;
    }
    
    return true;
}

// Cargar datos en formulario de edición
function cargarDatosEdicion(id, tipo) {
    // Esta función se implementa según el tipo de entidad
    // Se llama desde los botones de editar
    console.log(`Cargando datos para editar ${tipo} con ID: ${id}`);
}

// Ordenar tabla
function ordenarTabla(tableId, columna) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = Array.from(tbody.getElementsByTagName('tr'));
    
    rows.sort((a, b) => {
        const aText = a.getElementsByTagName('td')[columna].textContent;
        const bText = b.getElementsByTagName('td')[columna].textContent;
        
        return aText.localeCompare(bText);
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

// Exportar tabla a CSV
function exportarCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let row of rows) {
        const cols = row.querySelectorAll('td, th');
        const csvRow = [];
        
        for (let col of cols) {
            csvRow.push('"' + col.textContent.replace(/"/g, '""') + '"');
        }
        
        csv.push(csvRow.join(','));
    }
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    
    a.href = url;
    a.download = filename + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Notificación toast para admin
function mostrarNotificacionAdmin(mensaje, tipo = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${tipo}`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '99999';
    toast.style.minWidth = '300px';
    toast.textContent = mensaje;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Agregar clase active al enlace del menú actual
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.admin-nav a');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
});