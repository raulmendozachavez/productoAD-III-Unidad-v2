@extends('layouts.app')

@section('title', 'Adopción - Sanando Huellitas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adopcion-styles.css') }}">
@endpush

@section('content')
<main class="adopcion-page">
    <div class="container">
        <section class="no-mascota" style="display: block; max-width: 600px; margin: 2rem auto;">
            <h1 style="font-size: 2rem; margin-bottom: 1.5rem; color: var(--dark-color);">No hay mascota seleccionada</h1>
            <p style="font-size: 1.1rem; line-height: 1.8; color: #666; margin-bottom: 2rem;">
                Por favor, selecciona una mascota para adoptar desde nuestra página
                de mascotas.
            </p>
            <a href="{{ route('mascotas.index') }}" class="btn-primary" style="display: inline-block; margin-top: 1rem;">Ver Mascotas Disponibles</a>
        </section>
    </div>
</main>
@endsection
