<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $table = 'carrito';
    protected $primaryKey = 'id_carrito';
    public $timestamps = false;
    
    protected $fillable = [
        'id_usuario',
        'id_producto',
        'cantidad',
        'fecha_agregado',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'fecha_agregado' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
