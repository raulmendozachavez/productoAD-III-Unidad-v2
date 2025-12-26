@extends('layouts.app')

@section('title', 'Mi Perfil - Sanando Huellitas')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/perfil-styles.css') }}">
@endsection

@section('content')
<main class="perfil-page">
    <div class="container">
        <h1 class="page-title">Mi Perfil</h1>
        
        <div class="perfil-container">
            <!-- COLUMNA IZQUIERDA: Información Personal -->
            <aside class="perfil-sidebar">
                <div class="perfil-info">
                    <h2>👤 Información Personal</h2>
                    <div class="info-item">
                        <strong>Nombre de Usuario:</strong> 
                        <span>{{ Auth::user()->nombre_usuario }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Nombre Completo:</strong> 
                        <span>{{ Auth::user()->nombre_completo }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Email:</strong> 
                        <span>{{ Auth::user()->email }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Teléfono:</strong> 
                        <span>{{ Auth::user()->telefono ?? 'No registrado' }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Dirección:</strong> 
                        <span>{{ Auth::user()->direccion ?? 'No registrada' }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Rol:</strong> 
                        <span class="rol-badge rol-{{ strtolower(Auth::user()->rol) }}">
                            {{ ucfirst(Auth::user()->rol) }}
                        </span>
                    </div>
                </div>
            </aside>

            <!-- COLUMNA DERECHA: Adopciones y Pedidos -->
            <div class="perfil-content">
                <!-- Sección: Mis Adopciones -->
                <section class="perfil-section">
                    <h2>📋 Mis Adopciones</h2>
                    @php
                        $adopciones = Auth::user()->adopciones()->with('mascota')->get();
                    @endphp
                    @if($adopciones->isEmpty())
                        <p class="sin-datos">No has realizado ninguna solicitud de adopción aún.</p>
                    @else
                        <div class="adopciones-list">
                            @foreach($adopciones as $adopcion)
                                <div class="adopcion-item">
                                    <h3>🐾 {{ $adopcion->mascota->nombre }}</h3>
                                    <p>
                                        <strong>Estado:</strong> 
                                        <span class="estado-badge estado-{{ strtolower($adopcion->estado) }}">
                                            {{ ucfirst($adopcion->estado) }}
                                        </span>
                                    </p>
                                    <p>
                                        <strong>Fecha de solicitud:</strong> 
                                        {{ $adopcion->fecha_solicitud->format('d/m/Y') }}
                                    </p>
                                    @if($adopcion->fecha_aprobacion)
                                    <p>
                                        <strong>Fecha de aprobación:</strong> 
                                        {{ $adopcion->fecha_aprobacion->format('d/m/Y') }}
                                    </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                <!-- Sección: Mis Pedidos -->
                <section class="perfil-section">
                    <h2>🛒 Mis Pedidos</h2>
                    @php
                        $pedidos = Auth::user()->pedidos()->orderBy('fecha_pedido', 'desc')->get();
                    @endphp
                    @if($pedidos->isEmpty())
                        <p class="sin-datos">No has realizado ningún pedido aún.</p>
                    @else
                        <div class="pedidos-list">
                            @foreach($pedidos as $pedido)
                                <div class="pedido-item">
                                    <div class="pedido-header">
                                        <h3>🛍️ Pedido #{{ $pedido->id_pedido }}</h3>
                                        <span class="estado-badge estado-{{ strtolower($pedido->estado) }}">
                                            {{ ucfirst($pedido->estado) }}
                                        </span>
                                    </div>
                                    <p>
                                        <strong>Total:</strong> 
                                        <span class="pedido-total">S/. {{ number_format($pedido->total, 2) }}</span>
                                    </p>
                                    <p>
                                        <strong>Fecha:</strong> 
                                        {{ $pedido->fecha_pedido->format('d/m/Y H:i') }}
                                    </p>
                                    @if($pedido->direccion_envio)
                                    <p>
                                        <strong>Dirección de envío:</strong> 
                                        {{ $pedido->direccion_envio }}
                                    </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
    // Animación suave al cargar
    document.addEventListener('DOMContentLoaded', function() {
        const items = document.querySelectorAll('.info-item, .adopcion-item, .pedido-item');
        
        items.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                item.style.transition = 'all 0.5s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, index * 50);
        });
    });
</script>
@endsection