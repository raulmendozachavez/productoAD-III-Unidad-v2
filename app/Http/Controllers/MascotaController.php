<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use Illuminate\Http\Request;

class MascotaController extends Controller
{
    public function index()
    {
        $mascotas = Mascota::where('estado', 'disponible')
            ->orderBy('fecha_ingreso', 'desc')
            ->paginate(10);
        
        return view('mascotas.index', compact('mascotas'));
    }

    public function show($id)
    {
        $mascota = Mascota::findOrFail($id);
        return view('mascotas.show', compact('mascota'));
    }
}
