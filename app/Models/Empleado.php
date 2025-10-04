<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'apellido', 'email', 'telefono', 'direccion', 'puesto', 'salario', 'fecha_ingreso'
    ];

    // Un empleado puede tener muchas agendas/citas
    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }

    // Un empleado puede generar muchos tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
