@extends('layouts.app')

@section('title', 'Adopción de Mascotas - Sanando Huellitas')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
@endpush

@section('content')
<!-- Video Hero Section -->
<section class="hero-video">
    <video autoplay muted loop playsinline class="hero-video-bg">
        <source src="{{ asset('videos/perros-hero.mp4') }}" type="video/mp4" />
        Tu navegador no soporta videos HTML5
    </video>
    <div class="hero-overlay">
        <div class="hero-content">
            <h2 class="hero-title">Dale una Segunda Oportunidad</h2>
            <p class="hero-subtitle">
                Miles de mascotas esperan un hogar lleno de amor
            </p>
            <a href="{{ route('mascotas.index') }}" class="btn-primary">Conoce a nuestras mascotas</a>
        </div>
    </div>
</section>

<!-- Sección: Sobre Nosotros -->
<section class="about-section">
    <div class="container">
        <h2 class="section-title">Sobre Nuestra Organización</h2>
        <div class="about-content">
            <div class="about-text">
                <p>
                    <strong>Sanando Huellitas</strong> es una organización sin fines de
                    lucro dedicada al rescate, rehabilitación y adopción responsable
                    de mascotas desde 2019. Nuestra misión es encontrar hogares
                    amorosos para animales abandonados y maltratados.
                </p>
                <p>
                    Con más de <strong>+30 adopciones exitosas</strong>, trabajamos
                    incansablemente para educar sobre la tenencia responsable de
                    mascotas y combatir el abandono animal.
                </p>
                <div class="stats">
                    <div class="stat-item">
                        <span class="stat-number">+30</span>
                        <span class="stat-label">Adopciones</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">+50</span>
                        <span class="stat-label">Rescates Anuales</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">6</span>
                        <span class="stat-label">Años de Experiencia</span>
                    </div>
                </div>
            </div>
            <div class="about-media">
                <img src="{{ asset('images/organizacion.jpg') }}" alt="Nuestro refugio" class="about-image" />
            </div>
        </div>
    </div>
</section>

<!-- Galería de Mascotas Destacadas (DINÁMICO) -->
<section class="featured-pets">
    <div class="container">
        <h2 class="section-title">Mascotas Destacadas</h2>
        <div class="pets-grid">
            @foreach($mascotas_destacadas as $mascota)
                <div class="pet-card">
                    <img src="{{ asset('images/mascotas/' . $mascota->imagen) }}" alt="{{ $mascota->nombre }}">
                    <div class="pet-info">
                        <h3>{{ $mascota->nombre }}</h3>
                        <p>{{ $mascota->raza }} • {{ $mascota->edad }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="featured-pets">
    <div class="container">
        <h2 class="section-title">Ubícanos</h2>
        <p style="text-align: center; margin-bottom: 1.25rem; color: #333;">Chimbote, 02800, Perú</p>
        <div id="refugio-map" style="height: 420px; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 26px rgba(4, 17, 34, 0.12);"></div>
        <div style="display: flex; justify-content: center; margin-top: 1.25rem;">
            <a class="btn-secondary" href="https://www.google.com/maps?q=-9.0799603102846,-78.55932654103958" target="_blank" rel="noopener">Cómo llegar</a>
        </div>
    </div>
</section>

<!-- Llamado a la acción -->
<section class="cta-section">
    <div class="container">
        <h2>¿Listo para Cambiar una Vida?</h2>
        <p>
            Cada adopción salva dos vidas: la del animal que adoptas y la del
            próximo que podemos rescatar
        </p>
        <a href="{{ route('mascotas.index') }}" class="btn-secondary">Ver Todas las Mascotas</a>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    const video = document.querySelector('.hero-video-bg');
    
    if (video) {
        video.addEventListener('loadedmetadata', () => {
            const reinicioCada = 10;
            setInterval(() => {
                if (video.currentTime >= reinicioCada) {
                    video.currentTime = 0;
                    video.play();
                }
            }, 500);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const mapEl = document.getElementById('refugio-map');
        if (!mapEl || typeof L === 'undefined') return;

        const coords = [-9.079086, -78.559359];
        const map = L.map('refugio-map', {
            scrollWheelZoom: false,
        }).setView(coords, 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        L.marker(coords)
            .addTo(map)
            .bindPopup('Sanando Huellitas - Refugio')
            .openPopup();
    });
</script>
@endpush

