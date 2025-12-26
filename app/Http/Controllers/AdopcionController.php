<?php

namespace App\Http\Controllers;

use App\Models\Adopcion;
use App\Models\Mascota;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdopcionController extends Controller
{
    public function index()
    {
        return view('adopcion.index');
    }

    public function show($id)
    {
        if (Auth::user() && Auth::user()->isAdmin()) {
            return redirect()->route('mascotas.index')->with('toast_error', 'Función no válida para admin');
        }

        $mascota = Mascota::findOrFail($id);
        $usuario = Auth::user();
        
        return view('adopcion.show', compact('mascota', 'usuario'));
    }

    public function store(Request $request)
    {
        if (Auth::user() && Auth::user()->isAdmin()) {
            return back()->with('toast_error', 'Función no válida para admin');
        }

        $request->validate([
            'id_mascota' => 'required|exists:mascotas,id_mascota',
            'acepta_terminos' => 'required',
            'acepta_visitas' => 'required',
        ]);

        $mascota = Mascota::findOrFail($request->id_mascota);

        if ($mascota->estado !== 'disponible') {
            // Registrar intento de adopción fallido - Mascota no disponible
            Auditoria::registrar(
                'adopcion_fallida',
                'adopciones',
                'Intento de adopción fallido - Mascota no disponible',
                ['mascota_id' => $mascota->id_mascota, 'estado' => $mascota->estado]
            );
            return back()->withErrors(['error' => 'Esta mascota ya no está disponible']);
        }

        // Verificar si ya existe una solicitud
        $existe = Adopcion::where('id_usuario', Auth::id())
            ->where('id_mascota', $request->id_mascota)
            ->exists();

        if ($existe) {
            // Registrar intento de adopción duplicado
            Auditoria::registrar(
                'adopcion_duplicada',
                'adopciones',
                'Intento de adopción duplicado',
                ['mascota_id' => $mascota->id_mascota, 'usuario_id' => Auth::id()]
            );
            return back()->withErrors(['error' => 'Ya has solicitado adoptar esta mascota']);
        }

        // Crear la adopción
        $adopcion = Adopcion::create([
            'id_usuario' => Auth::id(),
            'id_mascota' => $request->id_mascota,
            'estado' => 'pendiente',
            'notas' => 'Solicitud realizada desde el formulario web',
        ]);

        // Actualizar estado de la mascota
        $mascota->update(['estado' => 'en_proceso']);

        // Registrar adopción exitosa
        Auditoria::registrar(
            'solicitud_adopcion',
            'adopciones',
            'Nueva solicitud de adopción',
            null,
            [
                'adopcion_id' => $adopcion->id,
                'mascota_id' => $mascota->id_mascota,
                'usuario_id' => Auth::id(),
                'estado' => 'pendiente'
            ]
        );

        return redirect()->route('adopcion.success', $mascota->nombre);
    }

    public function success($nombre)
    {
        return view('adopcion.success', compact('nombre'));
    }
}
