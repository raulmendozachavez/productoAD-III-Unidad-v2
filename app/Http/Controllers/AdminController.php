<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\CasoRescate;
use App\Models\User;
use App\Models\Adopcion;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin()) {
                abort(403, 'No tienes permisos para acceder a esta sección');
            }
            return $next($request);
        });
    }

    public function index()
    {
        Auditoria::registrar('ver_dashboard', 'admin', 'Acceso al dashboard administrativo');

        $stats = [
            'mascotas_disponibles' => Mascota::where('estado', 'disponible')->count(),
            'mascotas_adoptadas' => Mascota::where('estado', 'adoptado')->count(),
            'mascotas_proceso' => Mascota::where('estado', 'en_proceso')->count(),
            'total_usuarios' => User::count(),
            'adopciones_pendientes' => Adopcion::where('estado', 'pendiente')->count(),
            'adopciones_aprobadas' => Adopcion::where('estado', 'aprobada')->count(),
            'total_productos' => Producto::count(),
            'total_pedidos' => Pedido::count(),
            'ventas_totales' => Pedido::sum('total') ?? 0,
        ];

        $ultimas_adopciones = Adopcion::with(['usuario', 'mascota'])
            ->orderBy('fecha_solicitud', 'desc')
            ->paginate(5, ['*'], 'adopciones_page');

        $ultimos_pedidos = Pedido::with('usuario')
            ->orderBy('fecha_pedido', 'desc')
            ->paginate(5, ['*'], 'pedidos_page');

        return view('admin.index', compact('stats', 'ultimas_adopciones', 'ultimos_pedidos'));
    }

    public function auditoria(Request $request)
    {
        Auditoria::registrar('ver_auditoria', 'admin', 'Acceso al registro de auditoría');
        
        // Obtener lista de usuarios para el select (CORREGIDO)
        $usuarios = User::select('id_usuario', 'nombre_completo as nombre', 'email', 'rol')
            ->whereIn('rol', ['admin', 'usuario'])
            ->orderBy('nombre_completo')
            ->get();

        $query = Auditoria::with('usuario')
            ->when($request->modulo, function($q) use ($request) {
                return $q->where('modulo', $request->modulo);
            })
            ->when($request->usuario, function($q) use ($request) {
                return $q->where('id_usuario', $request->usuario);
            })
            ->when($request->fecha_desde, function($q) use ($request) {
                return $q->whereDate('fecha_hora', '>=', $request->fecha_desde);
            })
            ->when($request->fecha_hasta, function($q) use ($request) {
                return $q->whereDate('fecha_hora', '<=', $request->fecha_hasta);
            })
            ->orderBy('fecha_hora', 'desc');

        $auditorias = $query->paginate(20);

        // Mantener los parámetros de filtro en la paginación
        if ($request->hasAny(['modulo', 'usuario', 'fecha_desde', 'fecha_hasta'])) {
            $auditorias->appends($request->only(['modulo', 'usuario', 'fecha_desde', 'fecha_hasta']));
        }

        return view('admin.auditoria', compact('auditorias', 'usuarios'));
    }

    public function pedidos()
    {
        Auditoria::registrar('ver_pedidos', 'pedidos', 'Acceso a listado de pedidos');

        $pedidos = Pedido::with(['usuario', 'detallePedidos.producto'])
            ->orderBy('fecha_pedido', 'desc')
            ->paginate(10);
        return view('admin.pedidos', compact('pedidos'));
    }

    public function adopciones()
    {
        Auditoria::registrar('ver_adopciones', 'adopciones', 'Acceso a listado de adopciones');

        $adopciones = Adopcion::with(['usuario', 'mascota'])
            ->orderBy('fecha_solicitud', 'desc')
            ->paginate(10);

        return view('admin.adopciones', compact('adopciones'));
    }

    public function actualizarEstadoAdopcion(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,aprobada,rechazada,completada',
        ]);

        $nuevoEstado = $request->input('estado');

        DB::beginTransaction();
        try {
            $adopcion = Adopcion::with('mascota')->findOrFail($id);
            $estadoAnterior = $adopcion->estado;

            $data = ['estado' => $nuevoEstado];
            if (in_array($nuevoEstado, ['aprobada', 'completada'], true) && !$adopcion->fecha_aprobacion) {
                $data['fecha_aprobacion'] = now();
            }
            $adopcion->update($data);

            if ($adopcion->mascota) {
                if ($nuevoEstado === 'pendiente') {
                    $adopcion->mascota->update(['estado' => 'en_proceso']);
                } elseif ($nuevoEstado === 'aprobada') {
                    $adopcion->mascota->update(['estado' => 'en_proceso']);
                } elseif ($nuevoEstado === 'completada') {
                    $adopcion->mascota->update(['estado' => 'adoptado']);
                } elseif ($nuevoEstado === 'rechazada') {
                    $tieneActivas = Adopcion::where('id_mascota', $adopcion->id_mascota)
                        ->whereIn('estado', ['pendiente', 'aprobada'])
                        ->exists();

                    if (!$tieneActivas) {
                        $adopcion->mascota->update(['estado' => 'disponible']);
                    }
                }
            }

            Auditoria::registrar(
                'actualizar_estado_adopcion',
                'adopciones',
                "Cambio de estado de adopción #{$id}: {$estadoAnterior} → {$nuevoEstado}",
                ['estado' => $estadoAnterior],
                ['estado' => $nuevoEstado]
            );

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('admin.adopciones')->with('error', 'No se pudo actualizar el estado de la adopción');
        }

        return redirect()->route('admin.adopciones')->with('success', 'Estado de adopción actualizado');
    }

    public function productos()
    {
        Auditoria::registrar('ver_productos', 'productos', 'Acceso a listado de productos');

        $productos = Producto::orderBy('categoria')
            ->orderBy('nombre')
            ->paginate(10);
        return view('admin.productos', compact('productos'));
    }

    public function crearProducto(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'nullable|string|max:50',
            'imagen_archivo' => 'nullable|file|mimes:jpg,jpeg,png',
            'stock' => 'required|integer|min:0',
        ]);

        $data = $request->only(['nombre', 'descripcion', 'precio', 'categoria', 'stock']);

        if ($request->hasFile('imagen_archivo')) {
            $file = $request->file('imagen_archivo');
            $nombre = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/productos'), $nombre);
            $data['imagen'] = $nombre;
        } else {
            $data['imagen'] = 'placeholder.jpg';
        }

        $producto = Producto::create($data);

        Auditoria::registrar(
            'crear_producto',
            'productos',
            "Producto creado: {$producto->nombre}",
            null,
            $data
        );

        return redirect()->route('admin.productos')->with('success', 'Producto agregado');
    }

    public function actualizarProducto(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'nullable|string|max:50',
            'imagen_archivo' => 'nullable|file|mimes:jpg,jpeg,png',
            'stock' => 'required|integer|min:0',
        ]);

        $producto = Producto::findOrFail($id);
        $datosAnteriores = $producto->toArray();
        
        $data = $request->only(['nombre', 'descripcion', 'precio', 'categoria', 'stock']);

        if ($request->hasFile('imagen_archivo')) {
            $file = $request->file('imagen_archivo');
            $nombre = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/productos'), $nombre);
            $data['imagen'] = $nombre;
        }

        $producto->update($data);

        Auditoria::registrar(
            'actualizar_producto',
            'productos',
            "Producto actualizado: {$producto->nombre}",
            $datosAnteriores,
            $producto->toArray()
        );

        return redirect()->route('admin.productos')->with('success', 'Producto actualizado');
    }

    public function eliminarProducto($id)
    {
        $producto = Producto::findOrFail($id);
        $datosProducto = $producto->toArray();

        if ($producto->carrito()->exists()) {
            $producto->carrito()->delete();
        }

        try {
            $producto->delete();

            Auditoria::registrar(
                'eliminar_producto',
                'productos',
                "Producto eliminado: {$datosProducto['nombre']}",
                $datosProducto,
                null
            );
        } catch (QueryException $e) {
            return redirect()->route('admin.productos')->with('error', 'No se puede eliminar el producto porque está relacionado con otros registros');
        }

        return redirect()->route('admin.productos')->with('success', 'Producto eliminado');
    }

    public function mascotas()
    {
        Auditoria::registrar('ver_mascotas', 'mascotas', 'Acceso a listado de mascotas');

        $mascotas = Mascota::with('casosRescate')
            ->orderBy('fecha_ingreso', 'desc')
            ->paginate(10);
        return view('admin.mascotas', compact('mascotas'));
    }

    public function crearMascota(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'tipo' => 'required|in:perros,gatos,otros',
            'raza' => 'nullable|string|max:50',
            'edad' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string',
            'fecha_ingreso' => 'nullable|date',
            'es_rescate' => 'required|boolean',
            'estado' => 'required|in:disponible,en_proceso,adoptado',
            'imagen_archivo' => 'nullable|file|mimes:jpg,jpeg,png',
        ]);

        if ($request->boolean('es_rescate')) {
            $request->validate([
                'urgencia' => 'required|in:baja,media,alta',
                'situacion' => 'required|string',
                'historia' => 'required|string',
                'tratamiento' => 'nullable|string',
            ]);
        }

        $data = $request->only(['nombre','tipo','raza','edad','descripcion','fecha_ingreso','estado']);
        $data['es_rescate'] = (bool) $request->input('es_rescate');
        $data['fecha_ingreso'] = $data['fecha_ingreso'] ?: now()->toDateString();

        if ($request->hasFile('imagen_archivo')) {
            $file = $request->file('imagen_archivo');
            $nombre = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/mascotas'), $nombre);
            $data['imagen'] = $nombre;
        } else {
            $data['imagen'] = 'placeholder.jpg';
        }

        $mascota = Mascota::create($data);

        if ($data['es_rescate']) {
            CasoRescate::create([
                'id_mascota' => $mascota->id_mascota,
                'situacion' => $request->input('situacion'),
                'historia' => $request->input('historia'),
                'tratamiento' => $request->input('tratamiento'),
                'urgencia' => $request->input('urgencia', 'media'),
                'fecha_rescate' => $data['fecha_ingreso'],
            ]);
        }

        Auditoria::registrar(
            'crear_mascota',
            'mascotas',
            "Mascota creada: {$mascota->nombre} ({$mascota->tipo})",
            null,
            $data
        );

        return redirect()->route('admin.mascotas')->with('success', 'Mascota agregada');
    }

    public function actualizarMascota(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'tipo' => 'required|in:perros,gatos,otros',
            'raza' => 'nullable|string|max:50',
            'edad' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string',
            'fecha_ingreso' => 'nullable|date',
            'es_rescate' => 'required|boolean',
            'estado' => 'required|in:disponible,en_proceso,adoptado',
            'imagen_archivo' => 'nullable|file|mimes:jpg,jpeg,png',
        ]);
        
        if ($request->boolean('es_rescate')) {
            $request->validate([
                'urgencia' => 'required|in:baja,media,alta',
                'situacion' => 'required|string',
                'historia' => 'required|string',
                'tratamiento' => 'nullable|string',
            ]);
        }
        
        $mascota = Mascota::findOrFail($id);
        $datosAnteriores = $mascota->toArray();
        
        $data = $request->only(['nombre','tipo','raza','edad','descripcion','fecha_ingreso','estado']);
        $data['es_rescate'] = (bool) $request->input('es_rescate');
        
        if ($request->hasFile('imagen_archivo')) {
            $file = $request->file('imagen_archivo');
            $nombre = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/mascotas'), $nombre);
            $data['imagen'] = $nombre;
        }
        
        $mascota->update($data);

        if ($data['es_rescate']) {
            $rescate = CasoRescate::where('id_mascota', $mascota->id_mascota)->first();
            $datosRescate = [
                'situacion' => $request->input('situacion', 'Rescate marcado desde administración'),
                'historia' => $request->input('historia', 'Historia no detallada aún'),
                'tratamiento' => $request->input('tratamiento'),
                'urgencia' => $request->input('urgencia', 'media'),
                'fecha_rescate' => ($mascota->fecha_ingreso ?: now()->toDateString()),
            ];
            if ($rescate) {
                $rescate->update($datosRescate);
            } else {
                CasoRescate::create(array_merge($datosRescate, [
                    'id_mascota' => $mascota->id_mascota,
                ]));
            }
        }

        if (($data['estado'] ?? null) === 'disponible') {
            Adopcion::where('id_mascota', $mascota->id_mascota)
                ->where('estado', 'pendiente')
                ->update(['estado' => 'rechazada']);
        }

        Auditoria::registrar(
            'actualizar_mascota',
            'mascotas',
            "Mascota actualizada: {$mascota->nombre}",
            $datosAnteriores,
            $mascota->toArray()
        );

        return redirect()->route('admin.mascotas')->with('success', 'Mascota actualizada');
    }

    public function eliminarMascota($id)
    {
        $mascota = Mascota::findOrFail($id);
        $datosMascota = $mascota->toArray();

        if ($mascota->estado === 'en_proceso') {
            return redirect()->route('admin.mascotas')->with('error', 'No se puede eliminar una mascota en proceso');
        }

        if ($mascota->adopciones()->where('estado', '!=', 'rechazada')->exists()) {
            return redirect()->route('admin.mascotas')->with('error', 'No se puede eliminar una mascota que tiene solicitudes/adopciones activas');
        }

        DB::beginTransaction();
        try {
            $mascota->adopciones()->where('estado', 'rechazada')->delete();

            if ($mascota->casosRescate()->exists()) {
                $mascota->casosRescate()->delete();
            }

            $mascota->delete();

            Auditoria::registrar(
                'eliminar_mascota',
                'mascotas',
                "Mascota eliminada: {$datosMascota['nombre']}",
                $datosMascota,
                null
            );

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('admin.mascotas')->with('error', 'No se puede eliminar la mascota porque está relacionada con otros registros');
        }

        return redirect()->route('admin.mascotas')->with('success', 'Mascota eliminada');
    }

    public function actualizarEstadoPedido(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,procesando,enviado,entregado'
        ]);
        
        $pedido = Pedido::findOrFail($id);
        $estadoAnterior = $pedido->estado;
        
        $pedido->estado = $request->estado;
        $pedido->save();

        Auditoria::registrar(
            'actualizar_estado_pedido',
            'pedidos',
            "Cambio de estado de pedido #{$id}: {$estadoAnterior} → {$request->estado}",
            ['estado' => $estadoAnterior],
            ['estado' => $request->estado]
        );

        return redirect()->route('admin.pedidos')->with('success', 'Estado actualizado');
    }
}