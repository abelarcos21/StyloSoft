<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'codigo', 'descripcion', 'precio_venta', 'precio_compra', 'stock', 'activo'
    ];

    public function detalleTickets()
    {
        return $this->morphMany(DetalleTicket::class, 'vendible');
    }
}
