@extends('layouts.app')

@section('title', 'Adopción - Sanando Huellitas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adopcion-styles.css') }}">
@endpush

@section('content')
<main class="adopcion-page">
    <div class="container">
        <section class="adopcion-container">
            <h1 class="page-title">Proceso de Adopción</h1>
            <div class="adopcion-info">
                <div class="info-card">
                    <h2>✅ ¡Estás listo para adoptar!</h2>
                    <p>
                        Por favor verifica que toda la información sea correcta antes de
                        confirmar la adopción.
                    </p>
                </div>

                <div class="info-card">
                    <h3>👤 Información del Adoptante</h3>
                    <p><strong>Nombre de usuario:</strong> {{ $usuario->nombre_usuario }}</p>
                    <p><strong>Nombre completo:</strong> {{ $usuario->nombre_completo }}</p>
                    <p><strong>Correo electrónico:</strong> {{ $usuario->email }}</p>
                    <p><strong>Teléfono:</strong> {{ $usuario->telefono ?? 'No registrado' }}</p>
                    <p><strong>Dirección:</strong> {{ $usuario->direccion ?? 'No registrada' }}</p>
                </div>

                <div class="info-card mascota-info-card">
                    <h3>🐾 Información de la Mascota</h3>
                    <img src="{{ asset('images/mascotas/' . $mascota->imagen) }}" 
                         alt="{{ $mascota->nombre }}"
                         style="width: 100%; max-width: 300px; border-radius: 12px; margin: 1rem 0;">
                    <p><strong>Nombre:</strong> {{ $mascota->nombre }}</p>
                    <p><strong>Raza:</strong> {{ $mascota->raza }}</p>
                    <p><strong>Edad:</strong> {{ $mascota->edad }}</p>
                    <p><strong>Descripción:</strong> {{ $mascota->descripcion }}</p>
                </div>

                <div class="info-card requisitos-card">
                    <h3>📋 Requisitos de Adopción</h3>
                    <ul>
                        <li>✓ Ser mayor de 18 años</li>
                        <li>✓ Contar con espacio adecuado para la mascota</li>
                        <li>✓ Compromiso de cuidado y atención</li>
                        <li>✓ Capacidad económica para cubrir necesidades básicas</li>
                        <li>✓ Aceptar visitas de seguimiento</li>
                    </ul>
                </div>

                @if($errors->any())
                    <div class="error-message">
                        ❌ {{ $errors->first() }}
                    </div>
                @endif

                <form class="adopcion-form" method="POST" action="{{ route('adopcion.store') }}">
                    @csrf
                    <input type="hidden" name="id_mascota" value="{{ $mascota->id_mascota }}">
                    
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="acepta_terminos" required>
                            Acepto los términos y condiciones de adopción responsable
                        </label>
                    </div>
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="acepta_visitas" required>
                            Acepto recibir visitas de seguimiento del refugio
                        </label>
                    </div>
                    <button type="submit" class="btn-confirmar">
                        ¡Confirmar Adopción! 💚
                    </button>
                </form>
            </div>
        </section>
    </div>
</main>
@endsection
