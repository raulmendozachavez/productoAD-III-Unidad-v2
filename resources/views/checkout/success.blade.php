@extends('layouts.app')

@section('title', '¡Compra Exitosa! - Sanando Huellitas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/checkout-styles.css') }}">
@endpush

@section('content')
<main class="checkout-success-page">
    <div class="container">
        <div class="success-card">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            
            <h1>¡Gracias por tu compra!</h1>
            
            <div class="order-details">
                <div class="detail-item">
                    <span class="detail-label">Número de pedido:</span>
                    <span class="detail-value">#{{ $pedido->id_pedido }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Estado:</span>
                    <span class="status-badge status-{{ strtolower($pedido->estado) }}">
                        {{ ucfirst($pedido->estado) }}
                    </span>
                </div>
                <div class="detail-item total">
                    <span class="detail-label">Total pagado:</span>
                    <span class="detail-value">S/. {{ number_format($pedido->total, 2) }}</span>
                </div>
            </div>
            
            <p class="order-message">
                Hemos enviado los detalles de tu pedido a <strong>{{ Auth::user()->email }}</strong>.
                Te notificaremos cuando el estado de tu pedido cambie.
            </p>
            
            <div class="order-actions">
                <a href="{{ route('productos.index') }}" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    Seguir Comprando
                </a>
                <a href="{{ route('home') }}" class="btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Ir al Inicio
                </a>
            </div>
            
            <div class="order-tips">
                <h3>Consejos para tu compra:</h3>
                <ul>
                    <li>Guarda tu número de pedido para cualquier consulta</li>
                    <li>Revisa tu correo electrónico para más detalles</li>
                    <li>El tiempo de entrega estimado es de 2-5 días hábiles</li>
                </ul>
            </div>
        </div>
    </div>
</main>

<style>
.success-card {
    background: white;
    max-width: 700px;
    margin: 2rem auto;
    padding: 3rem 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    text-align: center;
    position: relative;
    overflow: hidden;
}

.success-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e8f5e9;
    border-radius: 50%;
    animation: bounceIn 0.8s ease-out;
}

.success-card h1 {
    color: var(--primary-color);
    margin-bottom: 2rem;
    font-size: 2.2rem;
    font-weight: 700;
}

.order-details {
    background: #f9f9f9;
    border-radius: 10px;
    padding: 1.5rem;
    margin: 2rem 0;
    text-align: left;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.detail-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.detail-item.total {
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 2px solid #eee;
    font-weight: 600;
    font-size: 1.2rem;
}

.detail-label {
    color: #666;
}

.detail-value {
    font-weight: 500;
    color: #333;
}

.status-badge {
    display: inline-block;
    padding: 0.3rem 0.8rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-pendiente {
    background: #fff3e0;
    color: #f57c00;
}

.status-procesando {
    background: #e3f2fd;
    color: #1976d2;
}

.status-enviado {
    background: #e8f5e9;
    color: #388e3c;
}

.status-entregado {
    background: #f1f8e9;
    color: #689f38;
}

.order-message {
    color: #555;
    line-height: 1.7;
    margin-bottom: 2.5rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.order-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 2.5rem;
}

.btn-primary, .btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.7rem;
    padding: 0.9rem 2rem;
    border-radius: 50px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1rem;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, var(--brand-grad-start), var(--brand-grad-end));
    color: white;
    box-shadow: 0 4px 15px rgba(37, 144, 115, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 144, 115, 0.4);
}

.btn-secondary {
    background: white;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}

.btn-secondary:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
}

.order-tips {
    background: #f5f9ff;
    border-radius: 10px;
    padding: 1.5rem;
    text-align: left;
    margin-top: 2rem;
    border: 1px dashed #bbdefb;
}

.order-tips h3 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.order-tips h3::before {
    content: '💡';
    font-size: 1.2em;
}

.order-tips ul {
    padding-left: 1.5rem;
    margin: 0;
    color: #555;
}

.order-tips li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3) translateY(20px);
    }
    50% {
        opacity: 1;
        transform: scale(1.1);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
    }
}

@media (max-width: 768px) {
    .success-card {
        margin: 1rem;
        padding: 2rem 1.5rem;
    }
    
    .success-card h1 {
        font-size: 1.8rem;
    }
    
    .order-actions {
        flex-direction: column;
        gap: 0.8rem;
    }
    
    .btn-primary, .btn-secondary {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection

