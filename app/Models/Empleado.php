<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empleado extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'direccion',
        'puesto',
        'hora_entrada',
        'hora_salida',
        'dias_laborales',
        'salario',
        'comision_porcentaje',
        'fecha_ingreso',
        'fecha_salida',
        'estado',
        'foto',
        'especialidades',
    ];

    protected $casts = [
        'dias_laborales' => 'array', // JSON automático
        'hora_entrada' => 'datetime:H:i',
        'hora_salida' => 'datetime:H:i',
        'salario' => 'decimal:2',
        'comision_porcentaje' => 'decimal:2',
        'fecha_ingreso' => 'date',
        'fecha_salida' => 'date',
    ];

    // Relaciones
    public function agendas(): HasMany
    {
        return $this->hasMany(Agenda::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function detalleTickets(): HasMany
    {
        return $this->hasMany(DetalleTicket::class);
    }

    // Accessors
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeDisponiblesDia($query, string $dia)
    {
        return $query->where('estado', 'activo')
                     ->whereJsonContains('dias_laborales', $dia);
    }

    // Métodos de negocio
    public function estaDisponible(string $dia): bool
    {
        if ($this->estado !== 'activo') {
            return false;
        }

        $diasLaborales = $this->dias_laborales ?? [];
        return in_array($dia, $diasLaborales);
    }

    public function calcularComision(float $monto): float
    {
        return $monto * ($this->comision_porcentaje / 100);
    }
}
