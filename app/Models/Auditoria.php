<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';
    protected $primaryKey = 'id_auditoria';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'nombre_usuario',
        'accion',
        'modulo',
        'descripcion',
        'ip_address',
        'user_agent',
        'datos_anteriores',
        'datos_nuevos'
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'fecha_hora' => 'datetime'
    ];

    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Registra una acción en la auditoría
     */
    public static function registrar($accion, $modulo, $descripcion = null, $datosAnteriores = null, $datosNuevos = null)
    {
        $userId = null;
        $userName = 'Sistema';
        
        if (auth()->check()) {
            $user = auth()->user();
            $userId = $user->id_usuario ?? $user->id ?? null;
            $userName = $user->nombre_usuario ?? $user->name ?? 'Usuario Desconocido';
        }

        // Si estamos en consola (comandos, tareas programadas)
        if (app()->runningInConsole()) {
            $userName = 'Sistema (Consola)';
        }

        // Convertir arrays a JSON si son arrays
        if (is_array($datosAnteriores)) {
            $datosAnteriores = json_encode($datosAnteriores, JSON_UNESCAPED_UNICODE);
        }
        
        if (is_array($datosNuevos)) {
            $datosNuevos = json_encode($datosNuevos, JSON_UNESCAPED_UNICODE);
        }

        try {
            return self::create([
                'id_usuario' => $userId,
                'nombre_usuario' => $userName,
                'accion' => $accion,
                'modulo' => $modulo,
                'descripcion' => $descripcion,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => $datosNuevos,
                'fecha_hora' => now()
            ]);
        } catch (\Exception $e) {
            // Silenciar error de auditoría para no interrumpir el flujo principal
            // Log::error('Error al registrar auditoría: ' . $e->getMessage());
            return null;
        }
    }
}