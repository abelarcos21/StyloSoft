<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'genero',
        'acepta_marketing',
        'puntos_fidelidad',
        'ultima_visita',
        'notas',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'ultima_visita' => 'date',
        'acepta_marketing' => 'boolean',
        'puntos_fidelidad' => 'integer',
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

    // Accessors
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeConMarketing($query)
    {
        return $query->where('acepta_marketing', true);
    }

    // Métodos de negocio
    public function agregarPuntos(int $puntos): void
    {
        $this->increment('puntos_fidelidad', $puntos);
    }

    public function actualizarUltimaVisita(): void
    {
        $this->update(['ultima_visita' => now()]);
    }



}
