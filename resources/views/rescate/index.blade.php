@extends('layouts.app')

@section('title', 'Casos de Rescate - Sanando Huellitas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mascotas-styles.css') }}">
@endpush

@section('content')
<main class="rescate-page">
    <div class="container">
        <div class="rescate-header">
            <h1 class="page-title">Casos de Rescate</h1>
            <p class="rescate-subtitle">
                Estas son historias reales de animales que han sufrido abandono,
                maltrato o negligencia. Cada uno de ellos merece una segunda
                oportunidad y un hogar lleno de amor.
            </p>
        </div>

        <!-- Mini Menú de Filtro -->
        <div class="filtro-menu">
            <button type="button" data-filter="todos" class="active">Todos</button>
            <button type="button" data-filter="perros">Perros</button>
            <button type="button" data-filter="gatos">Gatos</button>
            <button type="button" data-filter="otros">Otros</button>
        </div>

        <div class="rescate-grid">
            @foreach($casos as $caso)
                <div class="rescate-card" data-tipo="{{ $caso->mascota->tipo }}">
                    <div class="rescate-image">
                        <img src="{{ asset('images/mascotas/' . $caso->mascota->imagen) }}" 
                             alt="{{ $caso->mascota->nombre }}"
                             onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                    </div>
                    <div class="rescate-content">
                        <h3>{{ $caso->mascota->nombre }}</h3>
                        <p class="rescate-info">
                            {{ $caso->mascota->raza }} • {{ $caso->mascota->edad }}
                        </p>
                        
                        <span class="badge-urgencia urgencia-{{ $caso->urgencia }}">
                            Urgencia: {{ ucfirst($caso->urgencia) }}
                        </span>
                        
                        <div class="rescate-situacion">
                            <h4>💔 Situación:</h4>
                            <p>{{ $caso->situacion }}</p>
                        </div>
                        
                        <div class="rescate-descripcion">
                            <h4>📖 Historia:</h4>
                            <p>{{ $caso->historia }}</p>
                        </div>
                        
                        <div class="rescate-tratamiento">
                            <h4>🏥 Tratamiento actual:</h4>
                            <p>{{ $caso->tratamiento }}</p>
                        </div>
                        
                        @if($caso->mascota->estado == 'disponible')
                            <a href="{{ route('adopcion.show', $caso->mascota->id_mascota) }}" class="btn-adoptar">
                                💚 Darle un Hogar
                            </a>
                        @else
                            <button class="btn-adoptar" disabled style="background: #95a5a6; cursor: not-allowed;">
                                {{ $caso->mascota->estado == 'adoptado' ? '✓ Adoptado' : '⏳ En proceso' }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if(method_exists($casos, 'hasPages') && $casos->hasPages())
            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $casos->links() }}
            </div>
        @endif

        @if(count($casos) === 0)
            <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 12px;">
                <h2>No hay casos de rescate registrados en este momento</h2>
                <p>Afortunadamente, todos nuestros rescatados han encontrado hogar 💚</p>
                <a href="{{ route('mascotas.index') }}" class="btn-primary">Ver Todas las Mascotas</a>
            </div>
        @endif

        <div class="rescate-info-section">
            <h2>¿Cómo puedes ayudar?</h2>
            <div class="ayuda-grid">
                <div class="ayuda-card">
                    <span class="ayuda-icon">🏠</span>
                    <h3>Adopta</h3>
                    <p>Dale un hogar permanente a un animal rescatado.</p>
                </div>
                <div class="ayuda-card">
                    <span class="ayuda-icon">💰</span>
                    <h3>Dona</h3>
                    <p>Ayuda a cubrir gastos médicos y alimentación.</p>
                </div>
                <div class="ayuda-card">
                    <span class="ayuda-icon">👥</span>
                    <h3>Voluntariado</h3>
                    <p>Dedica tiempo a cuidar y socializar animales.</p>
                </div>
                <div class="ayuda-card">
                    <span class="ayuda-icon">📢</span>
                    <h3>Difunde</h3>
                    <p>Comparte nuestras historias en redes sociales.</p>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/mascotas-script.js') }}"></script>
@endpush

