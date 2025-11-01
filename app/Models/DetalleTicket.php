<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'vendible_type',
        'vendible_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    // Relación polimórfica: puede ser Producto o Servicio
    public function vendible()
    {
        return $this->morphTo();
    }

    // Relación con ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Opción: calcular subtotal automáticamente
    public function setSubtotalAttribute($value)
    {
        $this->attributes['subtotal'] = $this->cantidad * $this->precio_unitario;
    }
}
