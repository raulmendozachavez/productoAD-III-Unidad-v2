<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('categoria')
            ->orderBy('nombre')
            ->paginate(10);
        
        return view('productos.index', compact('productos'));
    }
}
