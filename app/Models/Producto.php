<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_producto';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'categoria',
        'imagen',
        'stock',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function carrito()
    {
        return $this->hasMany(Carrito::class, 'id_producto');
    }

    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class, 'id_producto');
    }
}
