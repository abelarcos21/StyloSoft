<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'empleado_id', 'servicio_id', 'tipo', 'fecha_hora', 'estado'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function servicios(){

        return $this->belongsToMany(Servicio::class, 'agenda_servicio')
                    ->withTimestamps();
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

}
