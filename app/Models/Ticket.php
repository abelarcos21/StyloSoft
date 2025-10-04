<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
     use HasFactory;

    protected $fillable = [
        'cliente_id', 'empleado_id', 'total', 'metodo_pago', 'fecha'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function detalleTickets()
    {
        return $this->hasMany(DetalleTicket::class);
    }
}
