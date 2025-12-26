@extends('layouts.app')

@section('title', 'Tienda - Sanando Huellitas')

@section('content')
<main class="store-page">
    <div class="container">
        <h1 class="page-title">Tienda de Productos para Mascotas</h1>
        <p class="store-subtitle" style="margin-bottom: 30px">
            Todo lo que necesitas para cuidar a tu mejor amigo. Parte de las
            ganancias se destinan al rescate de animales.
        </p>

        <div class="productos-grid" id="productos-container">
            @foreach($productos as $producto)
                <div class="producto-card" data-id="{{ $producto->id_producto }}">
                    <div class="producto-image">
                        <img src="{{ asset('images/productos/' . $producto->imagen) }}" 
                             alt="{{ $producto->nombre }}"
                             onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                    </div>
                    <div class="producto-content">
                        <h3>{{ $producto->nombre }}</h3>
                        <p class="producto-descripcion">
                            {{ $producto->descripcion }}
                        </p>
                        <p class="producto-precio">S/. {{ number_format($producto->precio, 2) }}</p>
                        
                        @if($producto->stock > 0)
                            <button class="btn-agregar-carrito" 
                                    onclick="agregarAlCarrito(
                                        {{ $producto->id_producto }}, 
                                        '{{ addslashes($producto->nombre) }}', 
                                        {{ $producto->precio }}, 
                                        '{{ $producto->imagen }}'
                                    )">
                                🛒 Agregar al Carrito
                            </button>
                        @else
                            <button class="btn-agregar-carrito" disabled style="background: #ccc; cursor: not-allowed;">
                                ❌ Sin Stock
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($productos->isEmpty())
            <div style="text-align: center; padding: 4rem 2rem;">
                <h2>No hay productos disponibles en este momento 😢</h2>
                <p>Estamos reabasteciendo nuestro inventario. Por favor vuelve pronto.</p>
            </div>
        @endif
    </div>
</main>
@endsection

