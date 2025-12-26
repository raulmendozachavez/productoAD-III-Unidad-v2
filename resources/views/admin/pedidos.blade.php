<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pedidos - Admin</title>
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
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th, .admin-table td { padding: 0.75rem; border-bottom: 1px solid #eee; text-align: left; }
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
                <li><a href="{{ route('admin.pedidos') }}" class="active">📦 Pedidos</a></li>
                <li><a href="{{ route('admin.auditoria') }}">📋 Auditoría</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="admin-container">
                <h1>📦 Pedidos</h1>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidos as $pedido)
                            <tr>
                                <td>#{{ $pedido->id_pedido }}</td>
                                <td>{{ $pedido->usuario->nombre_completo }}</td>
                                <td>S/. {{ number_format((float) $pedido->total, 2) }}</td>
                                <td>{{ ucfirst($pedido->estado) }}</td>
                                <td>{{ $pedido->fecha_pedido }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.pedidos.estado', $pedido->id_pedido) }}" style="display:flex; gap:0.5rem; align-items:center;">
                                        @csrf
                                        <select name="estado">
                                            <option value="pendiente" {{ $pedido->estado==='pendiente'?'selected':'' }}>pendiente</option>
                                            <option value="procesando" {{ $pedido->estado==='procesando'?'selected':'' }}>procesando</option>
                                            <option value="enviado" {{ $pedido->estado==='enviado'?'selected':'' }}>enviado</option>
                                            <option value="entregado" {{ $pedido->estado==='entregado'?'selected':'' }}>entregado</option>
                                        </select>
                                        <button type="submit" class="btn-primary">Actualizar</button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    @foreach($pedido->detallePedidos as $detalle)
                                        <div style="display:flex; gap:1rem;">
                                            <span>• {{ $detalle->producto ? $detalle->producto->nombre : $detalle->producto_nombre }} (x{{ $detalle->cantidad }})</span>
                                            <span>S/. {{ number_format((float) $detalle->precio_unitario, 2) }}</span>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                        @if(count($pedidos) === 0)
                            <tr><td colspan="6" style="text-align:center; padding:1rem;">No hay pedidos</td></tr>
                        @endif
                    </tbody>
                </table>

                @if(method_exists($pedidos, 'hasPages') && $pedidos->hasPages())
                    <div style="margin-top: 1rem; display: flex; justify-content: center;">
                        {{ $pedidos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>

