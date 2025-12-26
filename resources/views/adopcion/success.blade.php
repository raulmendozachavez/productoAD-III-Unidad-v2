@extends('layouts.app')

@section('title', 'Adopción Exitosa - Sanando Huellitas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adopcion-styles.css') }}">
@endpush

@section('content')
<main class="adopcion-page">
    <div class="container">
        <section class="adopcion-exitosa" style="display: block;">
            <div class="success-animation">
                <div class="checkmark">✓</div>
            </div>
            <h1>¡Felicidades! 🎉</h1>
            <p class="success-message">
                Ahora <strong>{{ $nombre }}</strong> estará siempre contigo. Esperamos
                que lo cuides con mucho amor y cariño.
            </p>
            <div class="success-details">
                <p>📧 Recibirás un correo de confirmación con todos los detalles</p>
                <p>📋 En 48 horas nos pondremos en contacto para coordinar la entrega</p>
            </div>
            <a href="{{ route('mascotas.index') }}" class="btn-primary">Ver Más Mascotas</a>
            <a href="{{ route('home') }}" class="btn-secondary">Volver al Inicio</a>
        </section>
    </div>
</main>
@endsection
