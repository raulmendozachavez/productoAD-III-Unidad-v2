<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sanando Huellitas</title>
    <link rel="stylesheet" href="{{ asset('css/auth-styles.css') }}">
</head>
<body class="login-body">
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="{{ route('home') }}" style="text-decoration: none; color: inherit;">
                    <h1>🐾 Sanando Huellitas</h1>
                </a>
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
                    <button class="tab-btn" onclick="window.location.href='{{ route('login') }}'">Iniciar Sesión</button>
                    <button class="tab-btn active">Registrarse</button>
                </div>

                <div class="form-container">
                    <h2>Crea tu cuenta 🐾</h2>
                    
                    @if($errors->any())
                        <div class="error-message">
                            ❌ {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="form-group">
                            <label>Nombre de Usuario: *</label>
                            <input type="text" name="nombre_usuario" required 
                                   placeholder="Ej: juanito123"
                                   value="{{ old('nombre_usuario') }}">
                        </div>

                        <div class="form-group">
                            <label>Nombre Completo: *</label>
                            <input type="text" name="nombre_completo" required 
                                   placeholder="Ej: Juan Pérez"
                                   value="{{ old('nombre_completo') }}">
                        </div>

                        <div class="form-group">
                            <label>Email: *</label>
                            <input type="email" name="email" required 
                                   placeholder="tu@email.com"
                                   value="{{ old('email') }}"
                                   pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                                   title="Por favor ingresa un correo electrónico válido">
                        </div>

                        <div class="form-group">
                            <label>Teléfono:</label>
                            <input type="text" name="telefono" 
                                   placeholder="987654321"
                                   value="{{ old('telefono') }}">
                        </div>

                        <div class="form-group">
                            <label>Dirección:</label>
                            <input type="text" name="direccion" 
                                   placeholder="Av. Los Olivos 123"
                                   value="{{ old('direccion') }}">
                        </div>

                        <div class="form-group">
                            <label>Contraseña: *</label>
                            <input type="password" name="password" required 
                                   placeholder="5-20 caracteres"
                                   minlength="5"
                                   maxlength="20"
                                   pattern="(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{5,20}"
                                   title="La contraseña debe tener 5-20 caracteres e incluir al menos una mayúscula, un número y un carácter especial">
                            <small style="color: #666; display: block; margin-top: .25rem;">Debe incluir al menos una mayúscula, una minúscula, un número y un carácter especial.</small>
                        </div>

                        <div class="form-group">
                            <label>Confirmar Contraseña: *</label>
                            <input type="password" name="password_confirmation" required 
                                   placeholder="Repite tu contraseña"
                                   minlength="5"
                                   maxlength="20"
                                   pattern="(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{5,20}"
                                   title="La contraseña debe tener 5-20 caracteres e incluir al menos una mayúscula, una minúscula, un número y un carácter especial">
                        </div>

                        <button type="submit" class="btn-submit">
                            ✨ Crear Cuenta
                        </button>
                    </form>

                    <p style="text-align: center; margin-top: 1.5rem; color: #666;">
                        ¿Ya tienes cuenta? 
                        <a href="{{ route('login') }}" style="color: var(--primary-color); font-weight: bold;">
                            Inicia sesión aquí
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('js/auth-validations.js') }}"></script>
    <footer class="footer">
        <div class="container">
            <p class="footer-bottom">
                © 2025 Sanando Huellitas - Todos los derechos reservados
            </p>
        </div>
    </footer>
</body>
</html>
