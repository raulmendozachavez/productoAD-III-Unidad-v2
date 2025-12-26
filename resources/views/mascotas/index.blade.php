@extends('layouts.app')

@section('title', 'Mascotas Disponibles - Sanando Huellitas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mascotas-styles.css') }}">
@endpush

@section('content')
<main class="mascotas-page">
    <div class="container">
        <h1 class="page-title">Mascotas Disponibles para Adopción</h1>

        <!-- Mini Menú de Filtro -->
        <div class="filtro-menu">
            <button type="button" data-filter="todos" class="active">Todos</button>
            <button type="button" data-filter="perros">Perros</button>
            <button type="button" data-filter="gatos">Gatos</button>
            <button type="button" data-filter="otros">Otros</button>
        </div>

        <!-- Grid de mascotas (DINÁMICO) -->
        <div class="mascotas-grid">
            @foreach($mascotas as $mascota)
                <div class="mascota-card" data-tipo="{{ $mascota->tipo }}">
                    <div class="mascota-image">
                        <img src="{{ asset('images/mascotas/' . $mascota->imagen) }}" alt="{{ $mascota->nombre }}">
                    </div>
                    <div class="mascota-content">
                        <h3>{{ $mascota->nombre }}</h3>
                        <p class="mascota-raza">{{ $mascota->raza }} • {{ $mascota->edad }}</p>
                        <p class="mascota-descripcion">
                            {{ $mascota->descripcion }}
                        </p>
                        <a href="{{ route('adopcion.show', $mascota->id_mascota) }}" class="btn-adoptar">💚 Adoptar</a>
                    </div>
                </div>
            @endforeach
        </div>

        @if(method_exists($mascotas, 'hasPages') && $mascotas->hasPages())
            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $mascotas->links('vendor.pagination.green') }}
            </div>
        @endif

        @if(count($mascotas) === 0)
            <div style="text-align: center; padding: 4rem 2rem;">
                <h2>No hay mascotas disponibles en este momento 😢</h2>
                <p>Por favor revisa más tarde o contacta con el refugio.</p>
            </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/mascotas-script.js') }}"></script>
@endpush

