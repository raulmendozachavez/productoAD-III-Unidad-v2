<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adopcion extends Model
{
    protected $table = 'adopciones';
    protected $primaryKey = 'id_adopcion';
    public $timestamps = false;
    
    protected $fillable = [
        'id_usuario',
        'id_mascota',
        'fecha_solicitud',
        'fecha_aprobacion',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_aprobacion' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota');
    }
}
