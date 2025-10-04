<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 'vendible_id', 'vendible_type', 'cantidad', 'precio_unitario', 'subtotal'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function vendible()
    {
        return $this->morphTo(); // Esto apunta a Producto o Servicio
    }
}
