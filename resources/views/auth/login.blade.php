<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sanando Huellitas</title>
    <link rel="stylesheet" href="{{ asset('css/auth-styles.css') }}">
    <body class="login-body">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <h1>🐾 Sanando Huellitas</h1>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="{{ route('home') }}">Inicio</a></li>
                    <li><a href="{{ route('mascotas.index') }}">Mascotas</a></li>
                    <li><a href="{{ route('productos.index') }}">Store</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="login-page">
        <div class="container">
            <div class="login-container">
                <div class="login-tabs">
                    <button class="tab-btn active">Iniciar Sesión</button>
                    <button class="tab-btn" onclick="window.location.href='{{ route('register') }}'">Registrarse</button>
                </div>

                <div class="form-container">
                    <h2>Bienvenido de nuevo 👋</h2>
                    
                    @if($errors->any())
                        <div class="error-message">
                            ❌ {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" required 
                                   placeholder="tu@email.com"
                                   value="{{ old('email') }}"
                                   pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                                   title="Por favor ingresa un correo electrónico válido">
                        </div>

                        <div class="form-group">
                            <label>Contraseña:</label>
                            <input type="password" name="password" required 
                                   placeholder="Tu contraseña">
                        </div>

                        <button type="submit" class="btn-submit">
                            🔐 Iniciar Sesión
                        </button>
                    </form>

                    <p style="text-align: center; margin-top: 1.5rem; color: #666;">
                        ¿No tienes cuenta? 
                        <a href="{{ route('register') }}" style="color: var(--primary-color); font-weight: bold;">
                            Regístrate aquí
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p class="footer-bottom">
                © 2025 Sanando Huellitas - Todos los derechos reservados
            </p>
        </div>
    </footer>
</body>
</html>

