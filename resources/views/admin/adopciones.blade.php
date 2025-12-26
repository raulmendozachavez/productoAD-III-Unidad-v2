<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Adopciones - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-styles.css') }}">
    <style>
        .admin-nav a { box-shadow: none !important; }
        .admin-nav a:hover,
        .admin-nav a.active {
            background: rgba(127, 218, 137, 0.2) !important;
            transform: translateY(-2px) !important;
            box-shadow: none !important;
        }
    </style>
</head>
<body class="admin-page">
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="{{ route('home') }}" style="text-decoration: none; color: inherit;">
                    <h1>🐾 Sanando Huellitas - ADMIN</h1>
                </a>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="{{ route('home') }}">Ver Sitio</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" style="background:none;border:none;cursor:pointer;">Salir</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <nav class="admin-nav">
        <div class="container">
            <ul>
                <li><a href="{{ route('admin.index') }}">📊 Dashboard</a></li>
                <li><a href="{{ route('admin.mascotas') }}">🐕 Mascotas</a></li>
                <li><a href="{{ route('admin.productos') }}">🛍️ Productos</a></li>
                <li><a href="{{ route('admin.adopciones') }}" class="active">💚 Adopciones</a></li>
                <li><a href="{{ route('admin.pedidos') }}">📦 Pedidos</a></li>
                <li><a href="{{ route('admin.auditoria') }}">📋 Auditoría</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="admin-container">
                <h1>💚 Adopciones</h1>

                @if($errors->any())
                    <div class="alert alert-error">
                        <ul style="margin:0; padding-left: 1.2rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 0.75rem; border-bottom: 1px solid #eee; text-align: left;">ID</th>
                            <th style="padding: 0.75rem; border-bottom: 1px solid #eee; text-align: left;">Usuario</th>
                            <th style="padding: 0.75rem; border-bottom: 1px solid #eee; text-align: left;">Mascota</th>
                            <th style="padding: 0.75rem; border-bottom: 1px solid #eee; text-align: left;">Contacto</th>
                            <th style="padding: 0.75rem; border-bottom: 1px solid #eee; text-align: left;">Fecha</th>
                            <th style="padding: 0.75rem; border-bottom: 1px solid #eee; text-align: left;">Estado</th>
                            <th style="padding: 0.75rem; border-bottom: 1px solid #eee; text-align: left;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adopciones as $adopcion)
                            <tr>
                                <td style="padding: 0.75rem; border-bottom: 1px solid #eee;">#{{ $adopcion->id_adopcion }}</td>
                                <td style="padding: 0.75rem; border-bottom: 1px solid #eee;">
                                    @if($adopcion->usuario)
                                        <div style="font-weight: 600;">{{ $adopcion->usuario->nombre_completo }}</div>
                                        <div style="opacity: 0.85; font-size: 0.9rem;">{{ $adopcion->usuario->email }}</div>
                                    @else
                                        Usuario eliminado
                                    @endif
                                </td>
                                <td style="padding: 0.75rem; border-bottom: 1px solid #eee;">
                                    @if($adopcion->mascota)
                                        <img src="{{ asset('images/mascotas/' . $adopcion->mascota->imagen) }}"
                                             style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; vertical-align: middle; margin-right: 0.5rem;"
                                             onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                                        <div style="display:inline-block; vertical-align: middle;">
                                            <div style="font-weight: 600;">{{ $adopcion->mascota->nombre }}</div>
                                            <div style="opacity: 0.85; font-size: 0.9rem;">{{ ucfirst($adopcion->mascota->tipo) }} - {{ $adopcion->mascota->raza ?: 'Sin raza' }}</div>
                                        </div>
                                    @else
                                        Mascota eliminada
                                    @endif
                                </td>
                                <td style="padding: 0.75rem; border-bottom: 1px solid #eee;">
                                    @if($adopcion->usuario)
                                        <div>📞 {{ $adopcion->usuario->telefono ?: '—' }}</div>
                                        <div>📍 {{ $adopcion->usuario->direccion ?: '—' }}</div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td style="padding: 0.75rem; border-bottom: 1px solid #eee;">{{ optional($adopcion->fecha_solicitud)->format('d/m/Y H:i') }}</td>
                                <td style="padding: 0.75rem; border-bottom: 1px solid #eee;">{{ ucfirst($adopcion->estado) }}</td>
                                <td style="padding: 0.75rem; border-bottom: 1px solid #eee;">
                                    @if($adopcion->estado === 'pendiente')
                                        <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap: wrap;">
                                            <form method="POST" action="{{ route('admin.adopciones.estado', $adopcion->id_adopcion) }}" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="estado" value="aprobada">
                                                <button type="submit" class="btn-small btn-approve">✓ Aprobar</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.adopciones.estado', $adopcion->id_adopcion) }}" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="estado" value="rechazada">
                                                <button type="submit" class="btn-small btn-reject">✗ Rechazar</button>
                                            </form>
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('admin.adopciones.estado', $adopcion->id_adopcion) }}" style="display:flex; gap:0.5rem; align-items:center; flex-wrap: wrap;">
                                            @csrf
                                            <select name="estado">
                                                <option value="pendiente" {{ $adopcion->estado==='pendiente'?'selected':'' }}>pendiente</option>
                                                <option value="aprobada" {{ $adopcion->estado==='aprobada'?'selected':'' }}>aprobada</option>
                                                <option value="rechazada" {{ $adopcion->estado==='rechazada'?'selected':'' }}>rechazada</option>
                                                <option value="completada" {{ $adopcion->estado==='completada'?'selected':'' }}>completada</option>
                                            </select>
                                            <button type="submit" class="btn-primary">Actualizar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if(count($adopciones) === 0)
                            <tr><td colspan="7" style="text-align:center; padding:1rem;">No hay adopciones</td></tr>
                        @endif
                    </tbody>
                </table>

                @if(method_exists($adopciones, 'hasPages') && $adopciones->hasPages())
                    <div style="margin-top: 1rem; display: flex; justify-content: center;">
                        {{ $adopciones->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p class="footer-bottom">© 2025 Sanando Huellitas - Panel de Administración</p>
        </div>
    </footer>

    <script src="{{ asset('js/admin-script.js') }}"></script>
</body>
</html>
