<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <meta name="user-role" content="{{ Auth::check() ? Auth::user()->rol : '' }}">
    <title>@yield('title', 'Sanando Huellitas - Adopción de Mascotas')</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/perfil-styles.css') }}">
    @stack('styles')
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="{{ route('home') }}" style="text-decoration: none; color: inherit;">
                    <h1>🐾 Sanando Huellitas</h1>
                </a>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Inicio</a></li>
                    <li><a href="{{ route('mascotas.index') }}" class="{{ request()->routeIs('mascotas.*') ? 'active' : '' }}">Mascotas</a></li>
                    <li><a href="{{ route('rescate.index') }}" class="{{ request()->routeIs('rescate.*') ? 'active' : '' }}">Rescate</a></li>
                    <li><a href="{{ route('productos.index') }}" class="{{ request()->routeIs('productos.*') ? 'active' : '' }}">Store</a></li>
                    <li>
                        <a href="{{ route('carrito.index') }}" class="carrito-icon {{ request()->routeIs('carrito.*') ? 'active' : '' }}">
                            🛒 <span id="cart-count">0</span>
                        </a>
                    </li>
                    @auth
                        <li><a href="{{ route('perfil') }}" class="{{ request()->routeIs('perfil') ? 'active' : '' }}">👤 {{ Auth::user()->nombre_completo }}</a></li>
                        
                        @if(Auth::user()->isAdmin())
                            <li><a href="{{ route('admin.index') }}" style="background: linear-gradient(135deg, var(--brand-grad-start) 0%, var(--brand-grad-end) 100%); color: white; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none;">⚙️ Admin</a></li>
                        @endif
                        
                        <li>
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer; padding: 0; text-decoration: none;">Salir</button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}">Login</a></li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Contacto</h3>
                    <p>📧 info@hogarfeliz.com</p>
                    <p>📞 +51 123 456 789</p>
                    <p>📍 Chimbote, Ancash, Perú</p>
                </div>
                <div class="footer-section">
                    <h3>Horarios</h3>
                    <p>Lunes a Viernes: 9:00 - 18:00</p>
                    <p>Sábados: 10:00 - 14:00</p>
                    <p>Domingos: Cerrado</p>
                </div>
                <div class="footer-section">
                    <h3>Síguenos</h3>
                    <p>Facebook | Instagram | Twitter</p>
                </div>
            </div>
            <p class="footer-bottom">
                © 2024 Sanando Huellitas - Todos los derechos reservados
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/carrito.js') }}"></script>
    @if(session('toast_error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof mostrarNotificacion === 'function') {
                    mostrarNotificacion(@json('❌ ' . session('toast_error')), 'error');
                }
            });
        </script>
    @endif
    @stack('scripts')
</body>
</html>
