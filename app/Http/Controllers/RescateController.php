<?php

namespace App\Http\Controllers;

use App\Models\CasoRescate;
use Illuminate\Http\Request;

class RescateController extends Controller
{
    public function index()
    {
        $casos = CasoRescate::with('mascota')
            ->orderByRaw("FIELD(urgencia, 'alta', 'media', 'baja')")
            ->orderBy('fecha_rescate', 'desc')
            ->get();
        
        return view('rescate.index', compact('casos'));
    }
}
