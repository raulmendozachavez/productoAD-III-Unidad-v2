<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría - Admin</title>
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
                <li><a href="{{ route('admin.adopciones') }}">💚 Adopciones</a></li>
                <li><a href="{{ route('admin.pedidos') }}">📦 Pedidos</a></li>
                <li><a href="{{ route('admin.auditoria') }}" class="active">📋 Auditoría</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="admin-container">
                <h1>📋 Registro de Auditoría</h1>

                <!-- Sección de Filtros usando estilos existentes -->

<div class="filtros-auditoria-modern">
    <div class="filtros-header">
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            Filtros de Búsqueda
        </h2>
    </div>
    
    <form method="GET" action="{{ route('admin.auditoria') }}" class="filtros-form">
        <div class="filtros-grid">
            <!-- Módulo -->
            <div class="filtro-item">
                <label class="filtro-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Módulo
                </label>
                <select name="modulo" class="filtro-select">
                    <option value="">Todos los módulos</option>
                    <option value="admin">🔐 Admin</option>
                    <option value="mascotas">🐕 Mascotas</option>
                    <option value="productos">🛍️ Productos</option>
                    <option value="adopciones">💚 Adopciones</option>
                    <option value="pedidos">📦 Pedidos</option>
                </select>
            </div>

            <!-- Usuario -->
            <div class="filtro-item">
                <label class="filtro-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Usuario
                </label>
                <select name="usuario" class="filtro-select">
                    <option value="">Todos los usuarios</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id_usuario }}" {{ request('usuario') == $usuario->id_usuario ? 'selected' : '' }}>
                            {{ $usuario->nombre }} ({{ $usuario->email }}) - {{ ucfirst($usuario->rol) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha desde -->
            <div class="filtro-item">
                <label class="filtro-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Fecha desde
                </label>
                <input type="date" name="fecha_desde" class="filtro-input">
            </div>

            <!-- Fecha hasta -->
            <div class="filtro-item">
                <label class="filtro-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Fecha hasta
                </label>
                <input type="date" name="fecha_hasta" class="filtro-input">
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="filtros-actions">
            <button type="submit" class="btn-filtrar">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                Aplicar Filtros
            </button>
            <a href="{{ route('admin.auditoria') }}" class="btn-limpiar">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="1 4 1 10 7 10"></polyline>
                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                </svg>
                Limpiar
            </a>
        </div>
    </form>
</div>

                <!-- Tabla de Auditoría -->
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Usuario</th>
                            <th>Módulo</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                            <th>IP</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditorias as $audit)
                            <tr>
                                <td>{{ $audit->fecha_hora->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $audit->nombre_usuario }}</td>
                                <td>
                                    <span class="estado-badge estado-disponible" style="min-width: auto; padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                                        {{ ucfirst($audit->modulo) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $claseAccion = 'estado-disponible';
                                        if (str_contains($audit->accion, 'crear')) {
                                            $claseAccion = 'estado-disponible';
                                        } elseif (str_contains($audit->accion, 'actualizar')) {
                                            $claseAccion = 'estado-en_proceso';
                                        } elseif (str_contains($audit->accion, 'eliminar')) {
                                            $claseAccion = 'badge-rechazada';
                                        }
                                    @endphp
                                    <span class="estado-badge {{ $claseAccion }}" style="min-width: auto; padding: 0.35rem 0.75rem; font-size: 0.85rem;">
                                        {{ ucfirst(str_replace('_', ' ', $audit->accion)) }}
                                    </span>
                                </td>
                                <td style="font-size: 0.9rem; color: #666;">
                                    {{ $audit->descripcion ?? '-' }}
                                </td>
                                <td>{{ $audit->ip_address }}</td>
                                <td>
                                    @if($audit->datos_anteriores || $audit->datos_nuevos)
                                        <button class="btn-small btn-edit" 
                                                onclick="abrirModal('modal-detalle-{{ $audit->id_auditoria }}')">
                                            Ver
                                        </button>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem;">
                                    No hay registros de auditoría
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if(method_exists($auditorias, 'hasPages') && $auditorias->hasPages())
                    <div style="margin-top: 1rem; display: flex; justify-content: center;">
                        {{ $auditorias->links('vendor.pagination.green') }}
                    </div>
                @endif

                <!-- Modales de Detalle -->
                @foreach($auditorias as $audit)
                    @if($audit->datos_anteriores || $audit->datos_nuevos)
                        <div id="modal-detalle-{{ $audit->id_auditoria }}" class="modal-overlay" style="display:none;">
                            <div class="modal-content" style="max-width: 700px;">
                                <div class="modal-header">
                                    <h3>Detalles de Auditoría #{{ $audit->id_auditoria }}</h3>
                                    <button class="modal-close" onclick="cerrarModal('modal-detalle-{{ $audit->id_auditoria }}')">×</button>
                                </div>
                                <div style="padding: 1.5rem;">
                                    @if($audit->datos_anteriores)
                                        <h4 style="margin-bottom: 0.75rem; color: var(--teal-dark);">Datos Anteriores:</h4>
                                        <div style="background: #f5f5f5; padding: 1rem; border-radius: 8px; font-family: monospace; font-size: 0.85rem; max-height: 250px; overflow-y: auto; margin-bottom: 1.5rem;">
                                            <pre style="margin: 0; white-space: pre-wrap; word-wrap: break-word;">{{ json_encode($audit->datos_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    @endif

                                    @if($audit->datos_nuevos)
                                        <h4 style="margin-bottom: 0.75rem; color: var(--teal-dark);">Datos Nuevos:</h4>
                                        <div style="background: #f5f5f5; padding: 1rem; border-radius: 8px; font-family: monospace; font-size: 0.85rem; max-height: 250px; overflow-y: auto; margin-bottom: 1.5rem;">
                                            <pre style="margin: 0; white-space: pre-wrap; word-wrap: break-word;">{{ json_encode($audit->datos_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    @endif

                                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                                        <p style="margin: 0; font-weight: bold; margin-bottom: 0.5rem;">User Agent:</p>
                                        <p style="margin: 0; font-size: 0.85rem; color: #666; word-break: break-all;">{{ $audit->user_agent }}</p>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="button" class="btn-small btn-reject" 
                                            onclick="cerrarModal('modal-detalle-{{ $audit->id_auditoria }}')">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
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