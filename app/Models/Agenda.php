<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Agenda extends Model
{
    use HasFactory, SoftDeletes;

    /* protected $fillable = [ //antes
        'cliente_id','empleado_id', 'servicio_id', 'fecha_hora_inicio','fecha_hora_fin',
        'tipo','estado','total'
    ]; */

    protected $fillable = [
        'cliente_id',
        'empleado_id',
        'fecha_hora_inicio',
        'fecha_hora_fin',
        'tipo',
        'estado',
        'notas',
        'cancelado_por',
        'razon_cancelacion',
    ];

    protected $casts = [
        'fecha_hora_inicio' => 'datetime',
        'fecha_hora_fin' => 'datetime',
    ];

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(Servicio::class, 'agenda_servicio')
                    ->withPivot(['precio', 'duracion_minutos', 'orden'])
                    ->withTimestamps()
                    ->orderBy('orden');
    }

    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class);
    }

    // Accessors
    public function getDuracionTotalAttribute(): int
    {
        return $this->servicios->sum('pivot.duracion_minutos');
    }

    public function getTotalAttribute(): float
    {
        return $this->servicios->sum('pivot.precio');
    }

    public function getEsPasadaAttribute(): bool
    {
        return $this->fecha_hora_inicio->isPast();
    }

    public function getEsHoyAttribute(): bool
    {
        return $this->fecha_hora_inicio->isToday();
    }

    public function getHorasRestantesAttribute(): ?float
    {
        if ($this->es_pasada) {
            return null;
        }
        return $this->fecha_hora_inicio->diffInHours(now(), false);
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeConfirmadas($query)
    {
        return $query->where('estado', 'confirmada');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeDelDia($query, Carbon $fecha)
    {
        return $query->whereDate('fecha_hora_inicio', $fecha);
    }

    public function scopeDelEmpleado($query, int $empleadoId)
    {
        return $query->where('empleado_id', $empleadoId);
    }

    public function scopeFuturas($query)
    {
        return $query->where('fecha_hora_inicio', '>', now());
    }

    public function scopePasadas($query)
    {
        return $query->where('fecha_hora_inicio', '<', now());
    }

    // Métodos de negocio
    public function confirmar(): void
    {
        $this->update(['estado' => 'confirmada']);
    }

    public function completar(): void
    {
        $this->update(['estado' => 'completada']);
        $this->cliente->actualizarUltimaVisita();
    }

    public function cancelar(string $razon, string $canceladoPor = 'cliente'): void
    {
        $this->update([
            'estado' => 'cancelada',
            'razon_cancelacion' => $razon,
            'cancelado_por' => $canceladoPor,
        ]);
    }

    public function marcarNoAsistio(): void
    {
        $this->update(['estado' => 'no_asistio']);
    }

    public function hayConflictoHorario(): bool
    {
        return static::where('empleado_id', $this->empleado_id)
            ->where('id', '!=', $this->id)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->where(function ($query) {
                $query->whereBetween('fecha_hora_inicio', [$this->fecha_hora_inicio, $this->fecha_hora_fin])
                      ->orWhereBetween('fecha_hora_fin', [$this->fecha_hora_inicio, $this->fecha_hora_fin])
                      ->orWhere(function ($q) {
                          $q->where('fecha_hora_inicio', '<=', $this->fecha_hora_inicio)
                            ->where('fecha_hora_fin', '>=', $this->fecha_hora_fin);
                      });
            })
            ->exists();
    }

}
