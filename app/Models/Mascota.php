<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mascota extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_mascota';
    
    protected $fillable = [
        'nombre',
        'tipo',
        'raza',
        'edad',
        'descripcion',
        'imagen',
        'estado',
        'fecha_ingreso',
        'es_rescate',
    ];

    protected $casts = [
        'es_rescate' => 'boolean',
        'fecha_ingreso' => 'date',
    ];

    public function adopciones()
    {
        return $this->hasMany(Adopcion::class, 'id_mascota');
    }

    public function casosRescate()
    {
        return $this->hasMany(CasoRescate::class, 'id_mascota');
    }
}
