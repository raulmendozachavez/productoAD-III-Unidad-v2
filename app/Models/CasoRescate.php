<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasoRescate extends Model
{
    protected $table = 'casos_rescate';
    protected $primaryKey = 'id_rescate';
    public $timestamps = false;
    
    protected $fillable = [
        'id_mascota',
        'situacion',
        'historia',
        'tratamiento',
        'urgencia',
        'fecha_rescate',
    ];

    protected $casts = [
        'fecha_rescate' => 'date',
    ];

    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota');
    }
}
