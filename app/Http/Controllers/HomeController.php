<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $mascotas_destacadas = Mascota::where('estado', 'disponible')
            ->inRandomOrder()
            ->limit(3)
            ->get();
        
        return view('home', compact('mascotas_destacadas'));
    }
}
