<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{

    use HasFactory;

    protected $fillable = [
        'nombre', 'descripcion', 'precio', 'duracion_minutos', 'activo'
    ];

    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }

    public function detalleTickets()
    {
        return $this->morphMany(DetalleTicket::class, 'vendible');
    }
}
