// ========================================
// CARGAR CARRITO AL INICIAR PÁGINA
// ========================================
document.addEventListener('DOMContentLoaded', function () {
    const path = window.location.pathname;
    if (path.endsWith('/carrito') || path.includes('/carrito')) {
        cargarCarrito();
    }
});

// ========================================
// CARGAR PRODUCTOS DEL CARRITO
// ========================================
function cargarCarrito() {
    fetch(`${getBaseUrl()}/carrito/get`)
        .then(response => response.json())
        .then(items => {
            if (items && items.length > 0) {
                mostrarCarrito(items);
                calcularTotales(items);
            } else {
                mostrarCarritoVacio();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Evitar borrar el contenido si ya se renderizó por servidor y solo falló la actualización
            const carritoItems = document.getElementById('carrito-items');
            if (carritoItems && carritoItems.children.length === 0) {
                mostrarCarritoVacio();
            }
        });
}

// ========================================
// MOSTRAR PRODUCTOS EN EL CARRITO
// ========================================
function mostrarCarrito(items) {
    const carritoItems = document.getElementById('carrito-items');
    const carritoVacio = document.querySelector('.carrito-vacio');
    const carritoResumen = document.querySelector('.carrito-resumen');

    if (items.length === 0) {
        mostrarCarritoVacio();
        return;
    }

    // Mostrar resumen y ocultar mensaje de carrito vacío
    if (carritoResumen) carritoResumen.style.display = 'block';
    if (carritoVacio) carritoVacio.style.display = 'none';

    // Generar HTML de los items
    let html = '';
    items.forEach(item => {
        let imgPath = item.imagen;
        if (!imgPath.startsWith('http') && !imgPath.startsWith('/')) {
            imgPath = `${getBaseUrl()}/images/productos/${item.imagen}`;
        } else if (imgPath.startsWith('/')) {
            // Si empieza con /, asegurarse que tenga la base si no la tiene
            const baseUrl = getBaseUrl();
            if (baseUrl && !imgPath.startsWith(baseUrl)) {
                imgPath = `${baseUrl}${imgPath}`;
            }
        }

        html += `
            <div class="carrito-item" data-id="${item.id_carrito}">
                <img src="${imgPath}" alt="${item.nombre}" class="item-imagen">
                <div class="item-info">
                    <h3 class="item-nombre">${item.nombre}</h3>
                    <p class="item-precio">S/. ${parseFloat(item.precio).toFixed(2)}</p>
                    <div class="item-cantidad">
                        <button class="btn-cantidad" onclick="cambiarCantidad(${item.id_carrito}, ${item.cantidad - 1})">-</button>
                        <span class="cantidad-numero">${item.cantidad}</span>
                        <button class="btn-cantidad" onclick="cambiarCantidad(${item.id_carrito}, ${item.cantidad + 1})">+</button>
                    </div>
                </div>
                <div class="item-acciones">
                    <p class="item-subtotal">S/. ${(parseFloat(item.precio) * item.cantidad).toFixed(2)}</p>
                    <button class="btn-eliminar" onclick="eliminarDelCarrito(${item.id_carrito})">🗑️ Eliminar</button>
                </div>
            </div>
        `;
    });

    if (carritoItems) {
        carritoItems.innerHTML = html;
    }
}

// ========================================
// MOSTRAR CARRITO VACÍO
// ========================================
function mostrarCarritoVacio() {
    const carritoItems = document.getElementById('carrito-items');
    const carritoVacioEl = document.querySelector('.carrito-vacio');
    const carritoResumen = document.querySelector('.carrito-resumen');

    if (carritoItems) carritoItems.innerHTML = '';
    if (carritoVacioEl) {
        carritoVacioEl.style.display = 'block';
    } else if (carritoItems) {
        carritoItems.innerHTML = `
            <div class="carrito-vacio" style="display: block;">
                <div class="vacio-icon">🛒</div>
                <h2>Tu carrito está vacío</h2>
                <p>¡Agrega productos para comenzar tu compra!</p>
                <a href="${getBaseUrl()}/productos" class="btn-primary">Ir a la Tienda</a>
            </div>
        `;
    }
    if (carritoResumen) carritoResumen.style.display = 'none';
}

// ========================================
// CALCULAR TOTALES
// ========================================
function calcularTotales(items) {
    let subtotal = 0;

    items.forEach(item => {
        subtotal += parseFloat(item.precio) * item.cantidad;
    });

    const envio = subtotal > 0 ? 15.00 : 0;
    const total = subtotal + envio;

    // Actualizar en el DOM
    const subtotalEl = document.getElementById('subtotal');
    const envioEl = document.getElementById('envio');
    const totalEl = document.getElementById('total');

    if (subtotalEl) subtotalEl.textContent = `S/. ${subtotal.toFixed(2)}`;
    if (envioEl) envioEl.textContent = `S/. ${envio.toFixed(2)}`;
    if (totalEl) totalEl.innerHTML = `<strong>S/. ${total.toFixed(2)}</strong>`;
}

// ========================================
// CAMBIAR CANTIDAD
// ========================================
function cambiarCantidad(idCarrito, nuevaCantidad) {
    if (nuevaCantidad < 1) {
        eliminarDelCarrito(idCarrito);
        return;
    }
    fetch(`${getBaseUrl()}/carrito/${idCarrito}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ cantidad: nuevaCantidad })
    })
        .then(async response => {
            if (!response.ok) {
                try {
                    return await response.json();
                } catch (e) {
                    return { success: false, message: 'Error al actualizar cantidad' };
                }
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.message && typeof mostrarNotificacion === 'function') {
                    mostrarNotificacion(`⚠ ${data.message}`, 'error');
                }
                cargarCarrito();
                actualizarContadorCarrito();
            } else {
                mostrarNotificacion(data.message ? `❌ ${data.message}` : '❌ Error al actualizar cantidad', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('❌ Error de conexión', 'error');
        });
}

// ========================================
// ELIMINAR DEL CARRITO
// ========================================
function eliminarDelCarrito(idCarrito) {
    if (!confirm('¿Estás seguro de eliminar este producto?')) {
        return;
    }

    fetch(`${getBaseUrl()}/carrito/${idCarrito}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion('✓ Producto eliminado', 'success');
                cargarCarrito();
                actualizarContadorCarrito();
            } else {
                mostrarNotificacion('❌ Error al eliminar', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('❌ Error de conexión', 'error');
        });
}

// ========================================
// REALIZAR COMPRA
// ========================================
function realizarCompra() {
    const totalEl = document.getElementById('total');
    const total = totalEl ? parseFloat(totalEl.textContent.replace('S/.', '').replace(/\s/g, '').trim()) : 0;
    if (isNaN(total) || total === 0) {
        if (typeof mostrarNotificacion === 'function') {
            mostrarNotificacion('⚠ Tu carrito está vacío', 'error');
        }
        return;
    }
    window.location.href = `${getBaseUrl()}/checkout`;
}
