<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Productos - Admin</title>
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
                <li><a href="{{ route('admin.productos') }}" class="active">🛍️ Productos</a></li>
                <li><a href="{{ route('admin.adopciones') }}">💚 Adopciones</a></li>
                <li><a href="{{ route('admin.pedidos') }}">📦 Pedidos</a></li>
                <li><a href="{{ route('admin.auditoria') }}">📋 Auditoría</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="admin-container">
                <h1>🛍️ Productos</h1>

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

                <div style="display:flex; justify-content:flex-end; margin: 1rem 0;">
                    <button class="btn-small btn-approve" type="button" onclick="abrirModal('modal-crear-producto')">Agregar producto</button>
                </div>

                <div id="modal-crear-producto" class="modal-overlay" style="display:none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Agregar Producto</h3>
                            <button class="modal-close" onclick="cerrarModal('modal-crear-producto')">×</button>
                        </div>
                        <form class="admin-form" method="POST" action="{{ route('admin.productos.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div>
                                <label>Nombre</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}" required>
                            </div>
                            <div>
                                <label>Descripción</label>
                                <textarea name="descripcion">{{ old('descripcion') }}</textarea>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label>Precio</label>
                                    <input type="number" name="precio" step="0.01" min="0" value="{{ old('precio') }}" required>
                                </div>
                                <div>
                                    <label>Categoría</label>
                                    <input type="text" name="categoria" value="{{ old('categoria') }}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label>Imagen (subir imagen)</label>
                                    <input type="file" name="imagen_archivo" accept="image/*">
                                </div>
                                <div>
                                    <label>Stock</label>
                                    <input type="number" name="stock" min="0" value="{{ old('stock', 0) }}" required>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-small btn-approve">Guardar</button>
                                <button type="button" class="btn-small btn-reject" onclick="cerrarModal('modal-crear-producto')">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $p)
                            <tr>
                                <td>#{{ $p->id_producto }}</td>
                                <td>
                                    <img src="{{ asset('images/productos/' . $p->imagen) }}" alt="{{ $p->nombre }}"
                                         onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                                </td>
                                <td>{{ $p->nombre }}</td>
                                <td>{{ $p->categoria }}</td>
                                <td>S/. {{ number_format($p->precio, 2) }}</td>
                                <td>{{ $p->stock }}</td>
                                <td>
                                    <button class="btn-small btn-edit" onclick="abrirModal('modal-editar-prod-{{ $p->id_producto }}')">Editar</button>
                                    <form action="{{ route('admin.productos.destroy', $p->id_producto) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar producto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-small btn-delete">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if(count($productos) === 0)
                            <tr>
                                <td colspan="7" style="text-align:center; padding:1rem;">No hay productos</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                @foreach($productos as $p)
                    <div id="modal-editar-prod-{{ $p->id_producto }}" class="modal-overlay" style="display:none;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3>Editar Producto #{{ $p->id_producto }}</h3>
                                <button class="modal-close" onclick="cerrarModal('modal-editar-prod-{{ $p->id_producto }}')">×</button>
                            </div>
                            <form class="admin-form" method="POST" action="{{ route('admin.productos.update', $p->id_producto) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" value="{{ $p->nombre }}" required>
                                </div>
                                <div>
                                    <label>Descripción</label>
                                    <textarea name="descripcion">{{ $p->descripcion }}</textarea>
                                </div>
                                <div class="form-row">
                                    <div>
                                        <label>Precio</label>
                                        <input type="number" name="precio" step="0.01" min="0" value="{{ $p->precio }}" required>
                                    </div>
                                    <div>
                                        <label>Categoría</label>
                                        <input type="text" name="categoria" value="{{ $p->categoria }}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div>
                                        <label>Imagen (subir imagen)</label>
                                        <input type="file" name="imagen_archivo" accept="image/*">
                                    </div>
                                    <div>
                                        <label>Stock</label>
                                        <input type="number" name="stock" min="0" value="{{ $p->stock }}" required>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn-small btn-approve">Guardar</button>
                                    <button type="button" class="btn-small btn-reject" onclick="cerrarModal('modal-editar-prod-{{ $p->id_producto }}')">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>

    <script src="{{ asset('js/admin-script.js') }}"></script>
</body>
</html>
