<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Producto;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    public function index()
    {
        $carrito = Carrito::where('id_usuario', Auth::id())
            ->with('producto')
            ->get();

        if ($carrito->contains(function ($item) {
            return !$item->producto;
        })) {
            Carrito::where('id_usuario', Auth::id())
                ->whereDoesntHave('producto')
                ->delete();

            $carrito = Carrito::where('id_usuario', Auth::id())
                ->with('producto')
                ->get();
        }
        
        $subtotal = $carrito->sum(function ($item) {
            return $item->producto->precio * $item->cantidad;
        });
        
        $envio = $subtotal > 0 ? 15.00 : 0.00;
        $total = $subtotal + $envio;
        
        return view('carrito.index', compact('carrito', 'subtotal', 'envio', 'total'));
    }

    public function store(Request $request)
    {
        // Verificar si está autenticado
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'require_login' => true,
                'message' => 'Debes iniciar sesión'
            ], 401);
        }

        $request->validate([
            'id_producto' => 'required|exists:productos,id_producto',
            'cantidad' => 'required|integer|min:1',
        ]);

        $producto = Producto::findOrFail($request->id_producto);
        if ($producto->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Producto sin stock'
            ], 422);
        }

        $carritoItem = Carrito::where('id_usuario', Auth::id())
            ->where('id_producto', $request->id_producto)
            ->first();

        $cantidadSolicitada = (int) $request->cantidad;

        if ($carritoItem) {
            $nuevaCantidad = $carritoItem->cantidad + $cantidadSolicitada;
            if ($nuevaCantidad > $producto->stock) {
                $nuevaCantidad = (int) $producto->stock;
            }
            
            // Registrar actualización de cantidad en el carrito
            Auditoria::registrar(
                'actualizar_carrito',
                'carrito',
                'Actualización de cantidad en el carrito',
                ['id_producto' => $producto->id_producto, 'cantidad_anterior' => $carritoItem->cantidad],
                ['id_producto' => $producto->id_producto, 'nueva_cantidad' => $nuevaCantidad]
            );
            
            $carritoItem->update(['cantidad' => $nuevaCantidad]);

            if ($carritoItem->cantidad >= $producto->stock && $cantidadSolicitada > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cantidad ajustada al stock disponible'
                ]);
            }
        } else {
            $cantidadFinal = $cantidadSolicitada;
            if ($cantidadFinal > $producto->stock) {
                $cantidadFinal = (int) $producto->stock;
            }
            
            // Registrar nuevo ítem en el carrito
            $carrito = Carrito::create([
                'id_usuario' => Auth::id(),
                'id_producto' => $request->id_producto,
                'cantidad' => $cantidadFinal,
            ]);
            
            Auditoria::registrar(
                'agregar_carrito',
                'carrito',
                'Producto agregado al carrito',
                null,
                [
                    'id_carrito' => $carrito->id_carrito,
                    'id_producto' => $producto->id_producto,
                    'nombre_producto' => $producto->nombre,
                    'cantidad' => $cantidadFinal,
                    'precio_unitario' => $producto->precio
                ]
            );

            if ($cantidadSolicitada > $cantidadFinal) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cantidad ajustada al stock disponible'
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $carritoItem = Carrito::where('id_carrito', $id)
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        $producto = $carritoItem->producto;
        $cantidad = min($request->cantidad, $producto->stock);

        // Registrar actualización de cantidad
        Auditoria::registrar(
            'actualizar_cantidad_carrito',
            'carrito',
            'Actualización de cantidad en el carrito',
            [
                'id_carrito' => $carritoItem->id_carrito,
                'id_producto' => $producto->id_producto,
                'cantidad_anterior' => $carritoItem->cantidad
            ],
            [
                'id_carrito' => $carritoItem->id_carrito,
                'id_producto' => $producto->id_producto,
                'nueva_cantidad' => $cantidad
            ]
        );

        $carritoItem->update(['cantidad' => $cantidad]);

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada',
            'cantidad' => $cantidad,
            'stock' => $producto->stock
        ]);
    }

    public function destroy($id)
    {
        $carritoItem = Carrito::where('id_carrito', $id)
            ->where('id_usuario', Auth::id())
            ->with('producto')
            ->firstOrFail();

        // Registrar eliminación del carrito
        Auditoria::registrar(
            'eliminar_carrito',
            'carrito',
            'Producto eliminado del carrito',
            [
                'id_carrito' => $carritoItem->id_carrito,
                'id_producto' => $carritoItem->id_producto,
                'cantidad' => $carritoItem->cantidad,
                'producto' => $carritoItem->producto->nombre ?? 'Producto no encontrado'
            ]
        );

        $carritoItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito'
        ]);
    }

    public function getCart()
    {
        $carrito = Carrito::where('id_usuario', Auth::id())
            ->with('producto')
            ->get()
            ->map(function ($item) {
                if (!$item->producto) {
                    return null;
                }
                return [
                    'id_carrito' => $item->id_carrito,
                    'id_producto' => $item->id_producto,
                    'cantidad' => $item->cantidad,
                    'nombre' => $item->producto->nombre,
                    'precio' => $item->producto->precio,
                    'imagen' => $item->producto->imagen,
                    'stock' => $item->producto->stock,
                ];
            })
            ->filter()
            ->values();

        return response()->json($carrito);
    }

    public function getCartCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }
        $count = Carrito::where('id_usuario', Auth::id())->sum('cantidad');
        return response()->json(['count' => $count]);
    }
}