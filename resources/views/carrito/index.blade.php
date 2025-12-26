@extends('layouts.app')

@section('title', 'Carrito de Compras - Sanando Huellitas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/carrito-styles.css') }}">
@endpush

@section('content')
<main class="carrito-page">
    <div class="container">
        <h1 class="page-title">Mi Carrito de Compras</h1>

        <div class="carrito-container">
            <div class="carrito-items" id="carrito-items">
                @if($carrito->isEmpty())
                    <div class="carrito-vacio">
                        <div class="vacio-icon">🛒</div>
                        <h2>Tu carrito está vacío</h2>
                        <p>No hay productos en tu carrito. ¡Explora nuestra tienda y encuentra algo especial para tu mascota!</p>
                        <a href="{{ route('productos.index') }}" class="btn-primary">Explorar Productos</a>
                    </div>
                @else
                    @foreach($carrito as $item)
                        <div class="carrito-item" data-id="{{ $item->id_carrito }}">
                            <img src="{{ asset('images/productos/' . $item->producto->imagen) }}" 
                                 alt="{{ $item->producto->nombre }}" 
                                 class="item-imagen"
                                 loading="lazy">
                            <div class="item-info">
                                <h3 class="item-nombre">{{ $item->producto->nombre }}</h3>
                                <p class="item-precio">S/. {{ number_format($item->producto->precio, 2) }}</p>
                                <div class="item-cantidad">
                                    <button class="btn-cantidad" 
                                            onclick="cambiarCantidad({{ $item->id_carrito }}, {{ $item->cantidad - 1 }})"
                                            aria-label="Reducir cantidad">
                                        <span class="sr-only">Reducir cantidad</span>
                                        <span aria-hidden="true">−</span>
                                    </button>
                                    <span class="cantidad-numero" aria-live="polite">{{ $item->cantidad }}</span>
                                    <button class="btn-cantidad" 
                                            onclick="cambiarCantidad({{ $item->id_carrito }}, {{ $item->cantidad + 1 }})"
                                            aria-label="Aumentar cantidad">
                                        <span class="sr-only">Aumentar cantidad</span>
                                        <span aria-hidden="true">+</span>
                                    </button>
                                </div>
                            </div>
                            <div class="item-acciones">
                                <p class="item-subtotal">S/. {{ number_format($item->producto->precio * $item->cantidad, 2) }}</p>
                                <button class="btn-eliminar" 
                                        onclick="eliminarDelCarrito({{ $item->id_carrito }})"
                                        aria-label="Eliminar producto">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                    </svg>
                                    <span>Eliminar</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="carrito-resumen">
                <h2>Resumen del Pedido</h2>
                <div class="resumen-detalle">
                    <div class="resumen-linea">
                        <span>Subtotal:</span>
                        <span id="subtotal">S/. {{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="resumen-linea">
                        <span>Envío:</span>
                        <span id="envio">S/. {{ number_format($envio, 2) }}</span>
                    </div>
                    <div class="resumen-linea total">
                        <span>Total:</span>
                        <span id="total">S/. {{ number_format($total, 2) }}</span>
                    </div>
                </div>
                <button class="btn-realizar-compra" onclick="realizarCompra()" {{ $carrito->isEmpty() ? 'disabled' : '' }}>
                    <span class="button-text">Realizar Compra</span>
                    <span class="button-icon">→</span>
                </button>
                <a href="{{ route('productos.index') }}" class="btn-seguir-comprando">
                    <span class="button-text">Seguir Comprando</span>
                    <span class="button-icon">🛍️</span>
                </a>
            </div>
        </div>
    </div>
</main>

@if(!$carrito->isEmpty())
<div class="cart-mobile-actions">
    <div class="cart-total">
        <span>Total:</span>
        <strong>S/. {{ number_format($total, 2) }}</strong>
    </div>
    <button class="btn-realizar-compra" onclick="realizarCompra()">
        <span class="button-text">Realizar Compra</span>
        <span class="button-icon">→</span>
    </button>
</div>
@endif

<style>
/* Estilos para el carrito móvil */
.cart-mobile-actions {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    padding: 1rem;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    z-index: 1000;
    justify-content: space-between;
    align-items: center;
}

.cart-mobile-actions .cart-total {
    font-size: 1.2rem;
}

.cart-mobile-actions .btn-realizar-compra {
    margin: 0;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

@media (max-width: 768px) {
    .cart-mobile-actions {
        display: flex;
    }
    
    .carrito-page {
        padding-bottom: 80px;
    }
}

/* Mejoras de accesibilidad */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

/* Efectos de hover y focus */
button:focus, a:focus {
    outline: 3px solid rgba(37, 144, 115, 0.5);
    outline-offset: 2px;
}

/* Transiciones suaves */
button, a, .carrito-item {
    transition: all 0.2s ease-in-out;
}

/* Mejoras en los botones */
.btn-realizar-compra, .btn-seguir-comprando {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
}

.button-icon {
    display: inline-flex;
    align-items: center;
}
</style>

@endsection

@push('scripts')
<script src="{{ asset('js/carrito.js') }}"></script>
@endpush

