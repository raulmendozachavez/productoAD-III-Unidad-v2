<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel Admin - Sanando Huellitas</title>
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
<body>
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
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer; padding: 0; text-decoration: none;">Salir</button>
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
                <li><a href="{{ route('admin.adopciones') }}">💚 Adopciones</a></li>
                <li><a href="{{ route('admin.pedidos') }}">📦 Pedidos</a></li>
                <li><a href="{{ route('admin.auditoria') }}">📋 Auditoría</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="container">
            <h1 class="page-title">Panel de Administración</h1>

            <!-- Estadísticas -->
            <div class="dashboard-grid">
                <div class="stat-card">
                    <span class="stat-number">{{ $stats['mascotas_disponibles'] }}</span>
                    <span class="stat-label">Mascotas Disponibles</span>
                </div>
                
                <div class="stat-card">
                    <span class="stat-number">{{ $stats['mascotas_adoptadas'] }}</span>
                    <span class="stat-label">Mascotas Adoptadas</span>
                </div>
                
                <div class="stat-card">
                    <span class="stat-number">{{ $stats['total_usuarios'] }}</span>
                    <span class="stat-label">Usuarios Registrados</span>
                </div>
                
                <div class="stat-card">
                    <span class="stat-number">{{ $stats['adopciones_pendientes'] }}</span>
                    <span class="stat-label">Adopciones Pendientes</span>
                </div>
                
                <div class="stat-card">
                    <span class="stat-number">{{ $stats['total_productos'] }}</span>
                    <span class="stat-label">Productos en Tienda</span>
                </div>
                
                <div class="stat-card">
                    <span class="stat-number">S/. {{ number_format($stats['ventas_totales'], 2) }}</span>
                    <span class="stat-label">Ventas Totales</span>
                </div>
            </div>

            <!-- Últimas Adopciones -->
            <div class="recent-section">
                <h2>📋 Últimas Solicitudes de Adopción</h2>
                @if($ultimas_adopciones->isEmpty())
                    <p>No hay solicitudes de adopción recientes.</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Mascota</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimas_adopciones as $adopcion)
                                <tr>
                                    <td>{{ $adopcion->usuario->nombre_completo }}</td>
                                    <td>
                                        <img src="{{ asset('images/mascotas/' . $adopcion->mascota->imagen) }}" 
                                             style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; vertical-align: middle; margin-right: 0.5rem;">
                                        {{ $adopcion->mascota->nombre }}
                                    </td>
                                    <td>{{ $adopcion->fecha_solicitud->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $adopcion->estado }}">
                                            {{ ucfirst($adopcion->estado) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Últimos Pedidos -->
            <div class="recent-section">
                <h2>🛒 Últimos Pedidos</h2>
                @if($ultimos_pedidos->isEmpty())
                    <p>No hay pedidos recientes.</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimos_pedidos as $pedido)
                                <tr>
                                    <td>#{{ $pedido->id_pedido }}</td>
                                    <td>{{ $pedido->usuario->nombre_completo }}</td>
                                    <td>S/. {{ number_format($pedido->total, 2) }}</td>
                                    <td>{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $pedido->estado }}">
                                            {{ ucfirst($pedido->estado) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
