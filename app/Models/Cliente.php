<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'apellido', 'email', 'telefono', 'direccion'
    ];

    public function agendas(){
        return $this->hasMany(Agenda::class);
    }

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }
}
