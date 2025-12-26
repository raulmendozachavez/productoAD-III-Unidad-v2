<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $primaryKey = 'id_pedido';
    public $timestamps = false;
    
    protected $fillable = [
        'id_usuario',
        'total',
        'estado',
        'fecha_pedido',
        'direccion_envio',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'fecha_pedido' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido');
    }
}
