<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mascotas - Admin</title>
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
                <li><a href="{{ route('admin.mascotas') }}" class="active">🐕 Mascotas</a></li>
                <li><a href="{{ route('admin.productos') }}">🛍️ Productos</a></li>
                <li><a href="{{ route('admin.adopciones') }}">💚 Adopciones</a></li>
                <li><a href="{{ route('admin.pedidos') }}">📦 Pedidos</a></li>
                <li><a href="{{ route('admin.auditoria') }}">📋 Auditoría</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="admin-container">
                <h1>🐕 Mascotas</h1>
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
                    <button class="btn-small btn-approve" type="button" onclick="abrirModal('modal-crear-mascota')">Agregar mascota</button>
                </div>

                <div id="modal-crear-mascota" class="modal-overlay" style="display:none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Agregar Mascota</h3>
                            <button class="modal-close" onclick="cerrarModal('modal-crear-mascota')">×</button>
                        </div>
                        <form class="admin-form" method="POST" action="{{ route('admin.mascotas.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div>
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" value="{{ old('nombre') }}" required>
                                </div>
                                <div>
                                    <label>Tipo</label>
                                    <select name="tipo" required>
                                        <option value="perros" {{ old('tipo')==='perros'?'selected':'' }}>Perros</option>
                                        <option value="gatos" {{ old('tipo')==='gatos'?'selected':'' }}>Gatos</option>
                                        <option value="otros" {{ old('tipo')==='otros'?'selected':'' }}>Otros</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label>Raza</label>
                                    <input type="text" name="raza" value="{{ old('raza') }}">
                                </div>
                                <div>
                                    <label>Edad</label>
                                    <input type="text" name="edad" value="{{ old('edad') }}">
                                </div>
                            </div>
                            <div>
                                <label>Descripción</label>
                                <textarea name="descripcion">{{ old('descripcion') }}</textarea>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label>Fecha de ingreso</label>
                                    <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso') }}">
                                </div>
                                <div>
                                    <label>¿Es un rescate?</label>
                                    <select name="es_rescate" required onchange="toggleRescateFields('nuevo', this.value)">
                                        <option value="1" {{ old('es_rescate','0')==='1' ? 'selected' : '' }}>Sí</option>
                                        <option value="0" {{ old('es_rescate','0')==='0' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                            <div id="rescate-fields-nuevo" style="display: {{ old('es_rescate','0')==='1' ? 'block' : 'none' }}; margin-top: 1rem;">
                                <div class="form-row">
                                    <div>
                                        <label>Urgencia</label>
                                        <select name="urgencia">
                                            <option value="baja" {{ old('urgencia')==='baja'?'selected':'' }}>Baja</option>
                                            <option value="media" {{ old('urgencia','media')==='media'?'selected':'' }}>Media</option>
                                            <option value="alta" {{ old('urgencia')==='alta'?'selected':'' }}>Alta</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label>Tratamiento actual</label>
                                        <input type="text" name="tratamiento" value="{{ old('tratamiento') }}">
                                    </div>
                                </div>
                                <div>
                                    <label>Situación</label>
                                    <textarea name="situacion">{{ old('situacion') }}</textarea>
                                </div>
                                <div>
                                    <label>Historia</label>
                                    <textarea name="historia">{{ old('historia') }}</textarea>
                                </div>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label>Imagen (subir imagen)</label>
                                    <input type="file" name="imagen_archivo" accept="image/*">
                                </div>
                                <div>
                                    <label>Estado</label>
                                    <select name="estado" required>
                                        <option value="disponible" {{ old('estado','disponible')==='disponible'?'selected':'' }}>Disponible</option>
                                        <option value="en_proceso" {{ old('estado')==='en_proceso'?'selected':'' }}>En proceso</option>
                                        <option value="adoptado" {{ old('estado')==='adoptado'?'selected':'' }}>Adoptado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-small btn-approve">Guardar</button>
                                <button type="button" class="btn-small btn-reject" onclick="cerrarModal('modal-crear-mascota')">Cancelar</button>
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
                            <th>Tipo</th>
                            <th>Raza</th>
                            <th>Edad</th>
                            <th>Estado</th>
                            <th>Ingreso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mascotas as $m)
                            <tr>
                                <td>#{{ $m->id_mascota }}</td>
                                <td>
                                    <img src="{{ asset('images/mascotas/' . $m->imagen) }}" alt="{{ $m->nombre }}"
                                         onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                                </td>
                                <td>{{ $m->nombre }}</td>
                                <td>{{ ucfirst($m->tipo) }}</td>
                                <td>{{ $m->raza }}</td>
                                <td>{{ $m->edad }}</td>
                                <td>
                                    <span class="estado-badge estado-{{ $m->estado }}">{{ ucfirst($m->estado) }}</span>
                                </td>
                                <td>{{ $m->fecha_ingreso }}</td>
                                <td>
                                    <button class="btn-small btn-edit" onclick="abrirModalEditar({{ $m->id_mascota }})">Editar</button>
                                    <form action="{{ route('admin.mascotas.destroy', $m->id_mascota) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar mascota?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-small btn-delete">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if(method_exists($mascotas, 'hasPages') && $mascotas->hasPages())
                    <div style="margin-top: 1rem; display: flex; justify-content: center;">
                        {{ $mascotas->links('vendor.pagination.green') }}
                    </div>
                @endif

                <!-- Modal único reutilizable para editar -->
                <div id="modal-editar-mascota" class="modal-overlay" style="display:none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 id="modal-editar-titulo">Editar Mascota</h3>
                            <button class="modal-close" onclick="cerrarModal('modal-editar-mascota')">×</button>
                        </div>
                        <form id="form-editar-mascota" class="admin-form" method="POST" action="" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-row">
                                <div>
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" id="edit-nombre" required>
                                </div>
                                <div>
                                    <label>Tipo</label>
                                    <select name="tipo" id="edit-tipo" required>
                                        <option value="perros">Perros</option>
                                        <option value="gatos">Gatos</option>
                                        <option value="otros">Otros</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label>Raza</label>
                                    <input type="text" name="raza" id="edit-raza">
                                </div>
                                <div>
                                    <label>Edad</label>
                                    <input type="text" name="edad" id="edit-edad">
                                </div>
                            </div>
                            <div>
                                <label>Descripción</label>
                                <textarea name="descripcion" id="edit-descripcion"></textarea>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label>Fecha de ingreso</label>
                                    <input type="date" name="fecha_ingreso" id="edit-fecha-ingreso">
                                </div>
                                <div>
                                    <label>¿Es un rescate?</label>
                                    <select name="es_rescate" id="edit-es-rescate" required onchange="toggleRescateFields('editar', this.value)">
                                        <option value="1">Sí</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div id="rescate-fields-editar" style="display: none; margin-top: 1rem;">
                                <div class="form-row">
                                    <div>
                                        <label>Urgencia</label>
                                        <select name="urgencia" id="edit-urgencia">
                                            <option value="baja">Baja</option>
                                            <option value="media">Media</option>
                                            <option value="alta">Alta</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label>Tratamiento actual</label>
                                        <input type="text" name="tratamiento" id="edit-tratamiento">
                                    </div>
                                </div>
                                <div>
                                    <label>Situación</label>
                                    <textarea name="situacion" id="edit-situacion"></textarea>
                                </div>
                                <div>
                                    <label>Historia</label>
                                    <textarea name="historia" id="edit-historia"></textarea>
                                </div>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label>Imagen (subir imagen)</label>
                                    <input type="file" name="imagen_archivo" accept="image/*">
                                </div>
                                <div>
                                    <label>Estado</label>
                                    <select name="estado" id="edit-estado" required>
                                        <option value="disponible">Disponible</option>
                                        <option value="en_proceso">En proceso</option>
                                        <option value="adoptado">Adoptado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-small btn-approve">Guardar</button>
                                <button type="button" class="btn-small btn-reject" onclick="cerrarModal('modal-editar-mascota')">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    // Datos de las mascotas para el modal
                    const mascotasData = {};
                    @foreach($mascotas as $m)
                        @php 
                            $res = $m->casosRescate->isEmpty() ? null : $m->casosRescate->first();
                        @endphp
                        mascotasData[{{ $m->id_mascota }}] = {
                            id: {{ $m->id_mascota }},
                            nombre: @json($m->nombre),
                            tipo: @json($m->tipo),
                            raza: @json($m->raza ?? ''),
                            edad: @json($m->edad ?? ''),
                            descripcion: @json($m->descripcion ?? ''),
                            fecha_ingreso: @json($m->fecha_ingreso ? $m->fecha_ingreso->format('Y-m-d') : ''),
                            es_rescate: {{ $m->es_rescate ? 1 : 0 }},
                            estado: @json($m->estado),
                            urgencia: @json($res ? $res->urgencia : 'media'),
                            tratamiento: @json($res ? $res->tratamiento : ''),
                            situacion: @json($res ? $res->situacion : ''),
                            historia: @json($res ? $res->historia : '')
                        };
                    @endforeach
                </script>
            </div>
        </div>
    </main>
    <script>
        function toggleRescateFields(id, value) {
            const el = document.getElementById('rescate-fields-' + id);
            if (!el) return;
            el.style.display = (String(value) === '1') ? 'block' : 'none';
        }

        function abrirModalEditar(idMascota) {
            const mascota = mascotasData[idMascota];
            if (!mascota) return;

            // Llenar el formulario con los datos
            document.getElementById('modal-editar-titulo').textContent = 'Editar Mascota #' + mascota.id;
            document.getElementById('form-editar-mascota').action = '{{ route("admin.mascotas.update", ":id") }}'.replace(':id', mascota.id);
            document.getElementById('edit-nombre').value = mascota.nombre || '';
            document.getElementById('edit-tipo').value = mascota.tipo || 'perros';
            document.getElementById('edit-raza').value = mascota.raza || '';
            document.getElementById('edit-edad').value = mascota.edad || '';
            document.getElementById('edit-descripcion').value = mascota.descripcion || '';
            document.getElementById('edit-fecha-ingreso').value = mascota.fecha_ingreso || '';
            document.getElementById('edit-es-rescate').value = mascota.es_rescate || 0;
            document.getElementById('edit-estado').value = mascota.estado || 'disponible';
            document.getElementById('edit-urgencia').value = mascota.urgencia || 'media';
            document.getElementById('edit-tratamiento').value = mascota.tratamiento || '';
            document.getElementById('edit-situacion').value = mascota.situacion || '';
            document.getElementById('edit-historia').value = mascota.historia || '';

            // Mostrar/ocultar campos de rescate
            toggleRescateFields('editar', mascota.es_rescate);

            // Abrir el modal
            abrirModal('modal-editar-mascota');
        }
    </script>

    <script src="{{ asset('js/admin-script.js') }}"></script>
</body>
</html>
