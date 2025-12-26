<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $usuario = Auth::user();
        $carrito = Carrito::where('id_usuario', $usuario->id_usuario)
            ->with('producto')
            ->get();

        if ($carrito->isEmpty()) {
            return redirect()->route('carrito.index');
        }

        $subtotal = $carrito->sum(function ($item) {
            return $item->producto->precio * $item->cantidad;
        });
        $envio = $subtotal > 0 ? 15.00 : 0.00;
        $total = $subtotal + $envio;

        return view('checkout.index', compact('usuario', 'carrito', 'subtotal', 'envio', 'total'));
    }

    public function store(Request $request)
    {
        $usuario = Auth::user();
        $carrito = Carrito::where('id_usuario', $usuario->id_usuario)
            ->with('producto')
            ->get();

        if ($carrito->isEmpty()) {
            return redirect()->route('carrito.index');
        }

        if ($carrito->contains(function ($item) {
            return !$item->producto;
        })) {
            return back()->withErrors(['checkout' => 'Error al procesar la compra: producto no disponible.']);
        }

        $subtotal = $carrito->sum(function ($item) {
            return $item->producto->precio * $item->cantidad;
        });
        $envio = $subtotal > 0 ? 15.00 : 0.00;
        $total = $subtotal + $envio;

        DB::beginTransaction();
        try {
            $productosBloqueados = [];
            foreach ($carrito as $item) {
                $producto = Producto::where('id_producto', $item->id_producto)
                    ->lockForUpdate()
                    ->first();

                if (!$producto) {
                    DB::rollBack();
                    return back()->withErrors(['checkout' => 'Error al procesar la compra: producto no disponible.']);
                }

                if ($producto->stock < $item->cantidad) {
                    DB::rollBack();
                    return back()->withErrors(['checkout' => "Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock}."]);
                }

                $productosBloqueados[$item->id_carrito] = $producto;
            }

            $pedido = Pedido::create([
                'id_usuario' => $usuario->id_usuario,
                'total' => $total,
                'estado' => 'pendiente',
                'direccion_envio' => $usuario->direccion,
            ]);

            // Registrar creación del pedido
            $detallesPedido = [];

            foreach ($carrito as $item) {
                $producto = $productosBloqueados[$item->id_carrito] ?? $item->producto;

                $producto->decrement('stock', $item->cantidad);

                $detalleData = [
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $item->id_producto,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $producto ? $producto->precio : 0,
                ];

                if (Schema::hasColumn('detalle_pedidos', 'producto_nombre')) {
                    $detalleData['producto_nombre'] = $producto ? $producto->nombre : null;
                }
                if (Schema::hasColumn('detalle_pedidos', 'producto_categoria')) {
                    $detalleData['producto_categoria'] = $producto ? $producto->categoria : null;
                }
                if (Schema::hasColumn('detalle_pedidos', 'producto_imagen')) {
                    $detalleData['producto_imagen'] = $producto ? $producto->imagen : null;
                }

                $detalle = DetallePedido::create($detalleData);
                
                // Agregar detalle para el registro de auditoría
                $detallesPedido[] = [
                    'id_detalle' => $detalle->id_detalle_pedido,
                    'id_producto' => $item->id_producto,
                    'nombre' => $producto->nombre,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $producto->precio * $item->cantidad
                ];
            }
            
            // Registrar el pedido completo en auditoría
            Auditoria::registrar(
                'crear_pedido',
                'compras',
                'Nuevo pedido realizado',
                null,
                [
                    'id_pedido' => $pedido->id_pedido,
                    'total' => $pedido->total,
                    'estado' => $pedido->estado,
                    'fecha_pedido' => $pedido->fecha_pedido,
                    'productos' => $detallesPedido
                ]
            );
            
            // Registrar la eliminación del carrito
            $carrito->each(function($item) use ($usuario) {
                Auditoria::registrar(
                    'vaciar_carrito',
                    'carrito',
                    'Producto removido del carrito por compra completada',
                    [
                        'id_carrito' => $item->id_carrito,
                        'id_producto' => $item->id_producto,
                        'cantidad' => $item->cantidad
                    ]
                );
            });
            
            // Eliminar el carrito
            Carrito::where('id_usuario', $usuario->id_usuario)->delete();

            Carrito::where('id_usuario', $usuario->id_usuario)->delete();

            DB::commit();
        } catch (\Throwable $e) {
            Log::error('Checkout error', [
                'message' => $e->getMessage(),
                'type' => get_class($e),
            ]);
            if ($e instanceof \Illuminate\Database\QueryException) {
                Log::error('Checkout SQL', [
                    'sql' => $e->getSql(),
                    'bindings' => $e->getBindings(),
                ]);
            }
            DB::rollBack();
            Log::error('Error en checkout: ' . $e->getMessage());
            
            // Registrar error en el pedido
            Auditoria::registrar(
                'error_pedido',
                'compras',
                'Error al procesar el pedido: ' . $e->getMessage(),
                null,
                [
                    'usuario_id' => $usuario->id_usuario,
                    'error' => $e->getMessage(),
                    'carrito' => $carrito->toArray()
                ]
            );
            
            return back()->withErrors(['checkout' => 'Ocurrió un error al procesar tu pedido. Por favor, inténtalo de nuevo.']);
        }
        
        // Registrar éxito del pedido
        Auditoria::registrar(
            'pedido_exitoso',
            'compras',
            'Pedido completado exitosamente',
            null,
            ['id_pedido' => $pedido->id_pedido, 'total' => $pedido->total]
        );
        
        return redirect()->route('checkout.success', $pedido->id_pedido);
    }

    public function success($id)
    {
        $usuario = Auth::user();
        $pedido = Pedido::with('usuario')->findOrFail($id);
        if ($pedido->id_usuario != $usuario->id_usuario) {
            abort(403);
        }
        return view('checkout.success', compact('pedido'));
    }
}