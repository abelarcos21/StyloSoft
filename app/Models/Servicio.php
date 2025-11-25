<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;


class Servicio extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'duracion_minutos',
        'categoria',
        'orden',
        'requiere_deposito',
        'deposito_minimo',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'duracion_minutos' => 'integer',
        'orden' => 'integer',
        'requiere_deposito' => 'boolean',
        'deposito_minimo' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Relación polimórfica
    public function detalleTickets(): MorphMany
    {
        return $this->morphMany(DetalleTicket::class, 'vendible');
    }

    // Relación con agendas (muchos a muchos)
    public function agendas(): BelongsToMany
    {
        return $this->belongsToMany(Agenda::class, 'agenda_servicio')
                    ->withPivot(['precio', 'duracion_minutos', 'orden'])
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    // Métodos de negocio
    public function aplicarDescuento(float $porcentaje): float
    {
        return $this->precio * (1 - $porcentaje / 100);
    }

}
